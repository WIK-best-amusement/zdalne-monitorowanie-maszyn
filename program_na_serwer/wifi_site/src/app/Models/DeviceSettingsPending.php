<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeviceSettingsPending
 * 
 * @property int $id
 * @property int $device_id
 * @property int $option_id
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $sent_to_mqtt
 * 
 * @property Device $device
 * @property Option $option
 *
 * @package App\Models
 */
class DeviceSettingsPending extends Model
{
	protected $table = 'device_settings_pending';

	protected $casts = [
		'device_id' => 'int',
		'option_id' => 'int',
		'sent_to_mqtt' => 'int'
	];

	protected $fillable = [
		'device_id',
		'option_id',
		'value',
		'sent_to_mqtt'
	];

	public function device()
	{
		return $this->belongsTo(Device::class);
	}

	public function option()
	{
		return $this->belongsTo(Option::class);
	}

    public static function getAllPendingOptionsForDevice($deviceId) {
        $updated = [];
        $selectOptions = OptionsGroupsRole::getOnlySelectOptions();

        $options = self::where('device_id', $deviceId)
            ->where('device_id', $deviceId)->get();
        foreach ($options as $option) {
            if (array_key_exists($option->option_id, $selectOptions)) {
                if (isset($selectOptions[$option->option_id])) {
                    $value = $selectOptions[$option->option_id][$option->value];
                } else {
                    // first empty
                    $value = array_first($selectOptions[$option->option_id]);
                }
            } else {
                $value = $option->value;
            }
            $updated[] = ['pending' => $option->option_id, 'value' => $value];
        }
        return $updated;
    }

    public static function deleteByDeviceAndOption($deviceId, $optionId)
    {
        return self::where('device_id', $deviceId)
            ->where('option_id', $optionId)
            ->delete();
    }

    public static function add($optionValue, $deviceId, $optionId)
    {
        self::insert([
            'value' => $optionValue,
            'device_id' => $deviceId,
            'option_id' => $optionId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
