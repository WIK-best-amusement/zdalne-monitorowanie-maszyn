<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LocationsRole
 * 
 * @property int $id
 * @property string $role
 * @property int $sort
 * 
 * @property Collection|LocationsUser[] $locations_users_role
 * @property Collection|Option[] $options
 *
 * @package App\Models
 */
class LocationsRole extends Model
{
	protected $table = 'locations_roles';
	public $timestamps = false;

	protected $casts = [
		'sort' => 'int'
	];

	protected $fillable = [
		'role',
		'sort'
	];

	public function locations_users_role()
	{
		return $this->hasMany(LocationsUser::class, 'role');
	}

	public function options()
	{
		return $this->belongsToMany(Option::class, 'options_groups_roles', 'role_id')
					->withPivot('id', 'can_see');
	}
}
