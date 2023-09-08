<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class DeviceSetting
 *
 * @property int $id
 * @property int $device_id
 * @property int $option_id
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Device $device
 * @property Collection|DeviceSettingsHistory[] $device_settings_histories_device_setting
 *
 * @package App\Models
 */
class DeviceSetting extends Model
{
    protected $table = 'device_settings';

    protected $casts = [
        'device_id' => 'int',
        'option_id' => 'int'
    ];

    protected $fillable = [
        'device_id',
        'option_id',
        'value'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function device_settings_histories_device_setting()
    {
        return $this->hasMany(DeviceSettingsHistory::class);
    }

    public function getSettingName($deviceId, $settingId)
    {

    }

    public static function checkIfValueExistsInSettings($deviceId, $optionId, $optionValue)
    {
        return self::select('id')
            ->where('device_id', $deviceId)
            ->where('option_id', $optionId)
            ->where('value', $optionValue)
            ->first();
    }

    public static function getDetails($deviceId, $level = null)
    {
        $qb = DB::table('device_settings as d')
            ->select('d.id as setting_id', 'd.value', 'p.value as value_pending', 'd.updated_at', 'options.name', 'dev_rep', 'options.id as optionId', 'options_groups.order', 'options_groups.id as groupId', 'options_groups.name as location_name', 'options_types.type', 'options_types.min', 'options_types.max', 'options_types.values', 'options.level')
            ->join('options', 'option_id', '=', 'options.id')
            ->join('options_groups', 'options_groups.id', '=', 'group_id')
            ->join('options_types', 'options_types.id', '=', 'type_id')
            ->leftJoin('device_settings_pending as p', function ($join) {
                $join->on('p.device_id', '=', 'd.device_id')
                    ->on('p.option_id', '=', 'd.option_id');
            });

        if ($level !== null) {
            $qb->rightJoin('options_groups_roles as ogr', function ($join) use ($level) {
                $join->on('ogr.option_id', '=', 'options.id');
                $join->on('ogr.role_id', '=', DB::raw($level));
            });
        }
        $qb->where('d.device_id', $deviceId)
            ->orderBy('options_groups.order', 'asc')
            ->orderBy('options.order', 'asc')
            ->orderBy('dev_rep', 'asc');
        return $qb->get();
    }
}
