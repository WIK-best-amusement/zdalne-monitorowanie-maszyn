<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersRole
 * 
 * @property int $id
 * @property int $user_id
 * @property string $role
 * 
 * @property User $user
 *
 * @package App\Models
 */
class UsersRole extends Model
{
	protected $table = 'users_roles';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'role'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
