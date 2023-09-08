<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LocationsUser
 * 
 * @property int $id
 * @property int $location_id
 * @property int $user_id
 * @property int $role
 * 
 * @property Location $location
 *
 * @package App\Models
 */
class LocationsUser extends Model
{
    use SoftDeletes;

	protected $table = 'locations_users';
	public $timestamps = true;

	protected $casts = [
		'location_id' => 'int',
		'user_id' => 'int',
		'role' => 'int'
	];

	protected $fillable = [
		'location_id',
		'user_id',
		'role'
	];

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function role()
	{
		return $this->belongsTo(LocationsRole::class, 'role');
	}

    public static function isUserInLocation($locationId, $userId) {
        return self::where('location_id', $locationId)
            ->where('user_id', $userId)
            ->first();
    }

    public static function isDeviceInUserLocation($userId, $locationId)
    {
        return self::join('locations', 'locations.id', '=', 'locations_users.location_id')
            ->join('locations_devices', 'locations_devices.location_id', '=', 'locations.id')
            ->where('locations_users.user_id', $userId)
            ->where('locations_devices.device_id', $locationId)
            ->where('locations.team_id', \Session::get('teamId'))
            ->first();
    }

    public static function getUsersWithLocation($teamId) {
        return Location::select('locations.id as location_id', 'users.name', 'users.email', 'locations_users.role', 'users.id as user_id')
            ->join('locations_users', 'locations.id', '=', 'locations_users.location_id')
            ->join('users', 'locations_users.user_id', '=', 'users.id')
            ->where('team_id', $teamId)
            ->where('locations.deleted_at', NULL)
            ->where('locations_users.deleted_at', NULL)
            ->get();
    }

    public static function deleteByLocation($locationId) {
        return self::where(['location_id' => $locationId])
            ->delete();
    }
}
