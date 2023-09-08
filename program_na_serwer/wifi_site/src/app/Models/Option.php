<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Option
 * 
 * @property int $id
 * @property string $name
 * @property string $dev_rep
 * @property string $field_length
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $location_id
 * @property int $order
 * @property int $type_id
 * @property int $level
 * 
 * @property OptionsGroup $group
 * @property Collection|DeviceSettingsPending[] $device_settings_pendings_option
 * @property Collection|LocationsRole[] $locations_roles
 *
 * @package App\Models
 */
class Option extends Model
{
	protected $table = 'options';

	protected $casts = [
		'location_id' => 'int',
		'order' => 'int',
		'type_id' => 'int',
		'level' => 'int'
	];

	protected $fillable = [
		'name',
		'dev_rep',
		'field_length',
		'location_id',
		'order',
		'type_id',
		'level'
	];

	public function group()
	{
		return $this->belongsTo(OptionsGroup::class, 'location_id');
	}

	public function device_settings_pendings_option()
	{
		return $this->hasMany(DeviceSettingsPending::class);
	}

	public function locations_roles()
	{
		return $this->belongsToMany(LocationsRole::class, 'options_groups_roles', 'option_id', 'role_id')
					->withPivot('id', 'can_see');
	}
}
