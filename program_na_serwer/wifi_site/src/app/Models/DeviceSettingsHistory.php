<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeviceSettingsHistory
 * 
 * @property int $id
 * @property int $device_setting_id
 * @property int $value
 * @property Carbon $date
 * 
 * @property DeviceSetting $device_setting
 *
 * @package App\Models
 */
class DeviceSettingsHistory extends Model
{
	protected $table = 'device_settings_history';
	public $timestamps = false;

	protected $casts = [
		'device_setting_id' => 'int',
		'value' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'device_setting_id',
		'value',
		'date'
	];

	public function device_setting()
	{
		return $this->belongsTo(DeviceSetting::class);
	}

	public static function getHistory($settingId) {
	    return self::where('device_setting_id',$settingId)->orderBy('date', 'desc')->get();
    }
}
