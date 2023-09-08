<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\DeviceSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Device
 * 
 * @property int $id
 * @property string $serial_number
 * @property string $challenge_response
 * @property int $device_group_id
 * @property int $mode_id
 * @property int $type_id
 * @property string $aes_key
 * @property string $name
 * @property Carbon $last_seen
 * @property int $modem_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Modem $modem
 * @property Collection|DeviceProblem[] $device_problems_device
 * @property Collection|DeviceSetting[] $device_settings_device
 * @property Collection|DeviceSettingsPending[] $device_settings_pendings_device
 * @property Collection|Location[] $locations
 *
 * @package App\Models
 */
class Device extends Model
{
	protected $table = 'devices';

	protected $casts = [
		'challenge_response' => 'binary',
		'device_group_id' => 'int',
		'mode_id' => 'int',
		'type_id' => 'int',
		'aes_key' => 'binary',
		'modem_id' => 'int',
        'team_id' => 'int',
	];

	protected $dates = [
		'last_seen'
	];

	protected $fillable = [
		'serial_number',
		'challenge_response',
		'device_group_id',
		'mode_id',
		'type_id',
		'aes_key',
		'name',
		'last_seen',
		'modem_id',
        'team_id' => 'int',
	];

	public function modem()
	{
		return $this->belongsTo(Modem::class);
	}

	public function device_problems_device()
	{
		return $this->hasMany(DeviceProblem::class);
	}

	public function device_settings_device()
	{
		return $this->hasMany(DeviceSetting::class);
	}

	public function device_settings_pendings_device()
	{
		return $this->hasMany(DeviceSettingsPending::class);
	}

	public function locations()
	{
		return $this->belongsToMany(Location::class, 'locations_devices')
					->withPivot('id');
	}

    public static function getDevicesForTeamOwner()
    {
        return self::select('devices.id', 'devices.serial_number', 'challenge_response', 'mode_id', 'type_id', 'devices.name', 'last_seen', 'devices.created_at', 'locations.name as location_name', 'modems.rssi', 'modems.net_name')
            ->leftJoin('locations_devices', function($join)
            {
                $join->on('locations_devices.device_id', '=', 'devices.id');
                $join->whereNull('locations_devices.deleted_at');

            })
            ->leftJoin('locations', function($join)
            {
                $join->on('locations.id', '=', 'locations_devices.location_id');
                $join->whereNull('locations.deleted_at');

            })
            ->leftJoin('modems', 'modems.id', '=', 'devices.modem_id')
            ->where('devices.team_id', \Session::get('teamId'))->get();
    }

    public static function getDevicesForTeamMember($userId)
    {
        $teamId = \Session::get('teamId');
        return self::select('devices.id', 'devices.serial_number', 'challenge_response', 'mode_id', 'type_id', 'devices.name', 'last_seen', 'devices.created_at', 'locations.name as location_name', 'modems.rssi')
            ->leftJoin('modems', 'modems.id', '=', 'devices.modem_id')
            ->leftJoin('locations_devices', function($join)
            {
                $join->on('locations_devices.device_id', '=', 'devices.id');
                $join->whereNull('locations_devices.deleted_at');

            })
            ->join('locations_users', function ($join)  use ($teamId) {
                $join->on('locations_users.location_id', '=', 'locations_devices.location_id');
                $join->whereNull('locations_users.deleted_at');
            })
            ->join('locations', function ($join)  use ($teamId) {
                $join->on('locations.id', '=', 'locations_devices.location_id');
                $join->where('locations.team_id', '=', $teamId);
                $join->whereNull('locations.deleted_at');
            })
            ->where('locations_users.user_id', $userId)
            ->where('devices.team_id', \Session::get('teamId'))
            ->get();
    }

    public static function getDeviceByLocation($id)
    {
        $devices = DB::table('locations')
            ->select('devices.id', 'devices.serial_number', 'challenge_response', 'mode_id', 'type_id', 'devices.name', 'last_seen', 'devices.created_at', 'modems.rssi', 'locations.name as location_name')
            ->leftJoin('locations_devices', function($join)
            {
                $join->on('locations_devices.location_id', '=', 'locations.id');
                $join->whereNull('locations_devices.deleted_at');

            })
            ->join('devices', 'devices.id', '=', 'locations_devices.device_id')
            ->leftJoin('modems', 'modems.id', '=', 'devices.modem_id')
            ->where('locations.id', $id)
            ->where('devices.team_id', \Session::get('teamId'))
            ->get();
        return $devices;
    }

    public static function updateDeviceBasicSettings($requestData, $deviceId)
    {
        parse_str($requestData['device'], $options);

        if (isset($options['device-name'])) {
            $deviceName = $options['device-name'];
            Device::where('id', $deviceId)->update(['name' => $deviceName]);
        }
        LocationsDevice::removeDeviceFromAllLocations($deviceId);

        if (isset($options['device-location'])) {
            $locations = Location::where('id', (int) $options['device-location'])->where('team_id', \Session::get('teamId'))->get();

            if (count($locations) == 1) {
                LocationsDevice::addDeviceToLocation($options['device-location'], $deviceId);
            }
        }

        return $deviceName;
    }

    public static function updateDeviceAdditionalSettings($requestData, $deviceId)
    {
        if (!isset($requestData['options']) || $requestData['options'] == '') {
            return [];
        }
        parse_str($requestData['options'], $options);

        foreach ($options['option'] as $optionId => $optionValue) {
            if (isset($options['reset']) && key_exists($optionId, $options['reset'])) {
                DeviceSettingsPending::deleteByDeviceAndOption($deviceId, $optionId);
            } else {
                $valueExistsInSettings = DeviceSetting::checkIfValueExistsInSettings($deviceId, $optionId, $optionValue);

                if (!$valueExistsInSettings) {
                    DeviceSettingsPending::deleteByDeviceAndOption($deviceId, $optionId);
                    DeviceSettingsPending::add($optionValue, $deviceId, $optionId);
                }
            }
        }
        $updated = DeviceSettingsPending::getAllPendingOptionsForDevice($deviceId);
        return $updated;
    }
}
