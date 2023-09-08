<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TeamsUser
 * 
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * 
 * @property Team $team
 * @property User $user
 *
 * @package App\Models
 */
class TeamsUser extends Model
{
    use SoftDeletes;

	protected $table = 'teams_users';
	public $timestamps = false;

	protected $casts = [
		'team_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'team_id',
		'user_id'
	];

	public function team()
	{
		return $this->belongsTo(Team::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

    /**
     * @param integer $teamId
     * @return mixed
     */
    public static function getTeamMembers($teamId) {
        return self::distinct()
            ->select('users.*')
            ->join('users', 'users.id', '=', 'teams_users.user_id')
            ->where('teams_users.team_id', $teamId)
            ->get();
    }
}
