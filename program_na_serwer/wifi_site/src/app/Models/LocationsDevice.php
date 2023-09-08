<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LocationsDevice
 * 
 * @property int $id
 * @property int $location_id
 * @property int $device_id
 * 
 * @property Device $device
 * @property Location $location
 *
 * @package App\Models
 */
class LocationsDevice extends Model
{
    use SoftDeletes;
	protected $table = 'locations_devices';
	public $timestamps = true;

	protected $casts = [
		'location_id' => 'int',
		'device_id' => 'int'
	];

	protected $fillable = [
		'location_id',
		'device_id'
	];

	public function device()
	{
		return $this->belongsTo(Device::class);
	}

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

    public static function removeDeviceFromAllLocations($deviceId) {
        self::where('device_id', $deviceId)->delete();
    }

    public static function addDeviceToLocation($locationId, $deviceId) {
        $locationId = (int) $locationId;
        $deviceId = (int) $deviceId;
        self::insert(['device_id' => $deviceId, 'location_id' => $locationId]);
    }

    public static function getLocationsForDevice($deviceId, $column = 'locations.id')
    {
        return self::select('locations.id', 'locations.name')
            ->join('locations', 'locations.id', '=', 'locations_devices.location_id')
            ->where('device_id', $deviceId)->pluck($column)->toArray();
    }
}
