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
 * 
 * @property Location $location
 *
 * @package App\Models
 */
class LocationsSettlements extends Model
{
    use SoftDeletes;
	protected $table = 'locations_settlements';
	public $timestamps = true;

	protected $casts = [
		'location_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'location_id',
		'user_id'
	];

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

    public static function getUsersWithSettlements($teamId) {
        return self::select('locations_settlements.location_id', 'users.name', 'users.email', 'locations_settlements.user_id')
            ->join('locations', function ($join)  use ($teamId) {
                $join->on('locations.id', '=', 'locations_settlements.location_id')
                    ->where('locations.team_id', '=', $teamId);
            })
            ->join('users', 'users.id', '=', 'locations_settlements.user_id')
            ->where('locations_settlements.deleted_at', NULL)
            ->get();
    }

    public static function deleteByLocation($locationId) {
        return self::where(['location_id' => $locationId])
            ->delete();
    }
}
