<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Modem
 * 
 * @property int $id
 * @property string $serial_number
 * @property int $firmware_version
 * @property float $rssi
 * @property int $device_group_id
 * @property string $net_name
 * 
 * @property Collection|Device[] $devices_modem
 *
 * @package App\Models
 */
class Modem extends Model
{
	protected $table = 'modems';
	public $timestamps = false;

	protected $casts = [
		'firmware_version' => 'int',
		'rssi' => 'float',
		'device_group_id' => 'int',
        'team_id' => 'int'
	];

	protected $fillable = [
		'serial_number',
		'firmware_version',
		'rssi',
		'device_group_id',
		'net_name',
        'team_id'
	];

	public function devices_modem()
	{
		return $this->hasMany(Device::class);
	}
}
