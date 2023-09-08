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
 * Class Team
 * 
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|Location[] $locations_team_id
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Team extends Model
{
	protected $table = 'teams';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'name',
		'user_id'
	];

	public function locations_team_id()
	{
		return $this->hasMany(Location::class, 'team_id', 'user_id');
	}

	public function users()
	{
		return $this->belongsToMany(User::class, 'teams_users')
					->withPivot('id');
	}

    public static function createTeamIfNotExists($userId, $name) {
        $name = str_slug($name, '-');
        $team = self::where('user_id', $userId);

        if ($team->exists()) {
            return $team->value('id');
        } else {
            $team = self::create(['user_id' => $userId, 'name' => $name, 'created_at' => new \DateTime()]);
            self::addUserToTeam($userId, $team->id);
            return $team->id;
        }
    }

    public static function addUserToTeam($userId, $teamId) {
        if (!TeamsUser::where('user_id', $userId)->where('team_id', $teamId)->exists()) {
            TeamsUser::create(['user_id' => $userId, 'team_id' => $teamId]);
        }
    }

    public static function isDeviceInUserTeam($userId, $deviceId = 0)
    {
        return self::select('devices.id')
            ->join('devices', 'team_id', '=', 'teams.id')
            ->where('user_id', $userId)
            ->where('devices.id', $deviceId)
            ->first();
    }

    public static function getUserTeamByUserId($userId) {
        return self::where('user_id', $userId)->first();
    }

    public static function getTeamsWhereUserBelongsTo($userId) {
        $teamsWhereUserBelongsToTmp = DB::table('teams_users')
            ->select('teams.id', 'teams.name')
            ->distinct()
            ->leftjoin('teams', 'teams.id', '=', 'teams_users.team_id')
            ->where('teams_users.user_id', $userId)
            ->get();

        return $teamsWhereUserBelongsToTmp;
    }

    public static function getIdsOfTeamWhereUserBelongsTo($userId, $exclude = 0) {
        $arr = self::getTeamsWhereUserBelongsTo($userId);
        $ids = [];
        foreach ($arr as $item) {
            if ($exclude == $item->id) {
                continue;
            }
            $ids[] = $item->id;
        }
        return $ids;
    }
}
