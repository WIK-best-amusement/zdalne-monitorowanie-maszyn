<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Location
 *
 * @property int $id
 * @property int $team_id
 * @property string $name
 *
 * @property Collection|Device[] $devices
 * @property Collection|LocationsUser[] $locations_users_locations
 *
 * @package App\Models
 */
class Location extends Model
{
    use SoftDeletes;
    protected $table = 'locations';
    public $timestamps = true;

    protected $casts = [
        'team_id' => 'int'
    ];

    protected $fillable = [
        'team_id',
        'name'
    ];

    public function team_id()
    {
        return $this->belongsTo(Team::class, 'team_id', 'user_id');
    }

    public function devices()
    {
        return $this->belongsToMany(Device::class, 'locations_devices')
            ->withPivot('id');
    }

    public function locations_users_location()
    {
        return $this->hasMany(LocationsUser::class);
    }

    public static function getUserTypeInLocation($deviceId, $userId)
    {
        $deviceId = (int) $deviceId;
        $userId = (int) $userId;

        $userType = LocationsDevice::from('locations_devices')
            ->join('locations_users', function ($join) use ($userId, $deviceId) {
                $join->on('locations_users.location_id', '=', 'locations_devices.location_id')
                    ->on('locations_users.user_id', '=', DB::raw($userId));
                    })
                    ->where('device_id', $deviceId)
                    ->first();
        if ($userType) {
            return $userType->role;
        }
        return null;
    }

    public static function getLocationsByTeam($teamId) {
        return self::where('team_id', $teamId)->get();
    }

    public static function isUserOwnerOfLocation($locationId, $userId) {
        return self::join('teams_users', 'teams_users.team_id', '=', 'locations.team_id')
            ->where('teams_users.user_id', $userId)
            ->where('locations.id', $locationId)
            ->first();
    }

    public static function updateLocationsName($locationId, $name) {
        return self::where(['id' => $locationId])
            ->update(['name' => $name]);
    }

    public static function deleteLocation($locationId) {
        DB::transaction(function () use ($locationId) {
            LocationsUser::where(['location_id' => $locationId])->delete();
            LocationsDevice::where(['location_id' => $locationId])->delete();
            LocationsSettlements::where(['location_id' => $locationId])->delete();
            LocationsUsersProfits::where(['location_id' => $locationId])->delete();
            self::where('id', '=', $locationId)->delete();
        });
    }
}
