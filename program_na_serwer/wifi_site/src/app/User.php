<?php

/**
 * Created by Reliese Model.
 */

namespace App;

use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $origin
 * @property string $username
 * @property string $password_hash
 * 
 * @property Collection|Session[] $sessions_user
 * @property Collection|Team[] $teams
 * @property Collection|UsersRole[] $users_roles_user
 *
 * @package App\Models
 */
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
	protected $table = 'users';

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'email',
		'password',
		'remember_token',
		'origin',
		'username',
		'password_hash'
	];

	public function sessions_user()
	{
		return $this->hasMany(Session::class);
	}

	public function teams()
	{
		return $this->belongsToMany(Team::class, 'teams_users')
					->withPivot('id');
	}

	public function users_roles_user()
	{
		return $this->hasMany(UsersRole::class);
	}

    public static function getUserByEmail($email) {
        return self::where('email', $email);
    }

    public static function updateUserName($userId, $name) {
        self::where(['id' => $userId])
            ->update(['name' => $name]);
    }

    public static function createUser($name, $email, $password, $origin = null) {
        $user = self::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'origin' => $origin
        ]);

        Team::createTeamIfNotExists($user->id, $user->email);

        return $user;
    }
}
