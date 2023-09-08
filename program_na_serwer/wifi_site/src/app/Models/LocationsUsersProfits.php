<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LocationsUsersProfits
 * 
 * @property int $id
 * @property int $location_id
 * @property int $user_id
 * @property double $profit
 * 
 * @property Location $location
 *
 * @package App\Models
 */
class LocationsUsersProfits extends Model
{
    use SoftDeletes;
	protected $table = 'locations_users_profits';
	public $timestamps = true;

	protected $casts = [
		'location_id' => 'int',
		'user_id' => 'int',
        'profit' => 'double'
	];

	protected $fillable = [
		'location_id',
		'user_id',
        'profit'
	];

    public static function getUsersWithProfits($locationsIds) {
        return self::select('locations_users_profits.location_id', 'locations_users_profits.profit', 'users.name', 'users.email', 'locations_users_profits.user_id')
            ->whereIn('locations_users_profits.location_id', $locationsIds)
            ->join('users', 'users.id', '=', 'locations_users_profits.user_id')
            ->where('locations_users_profits.deleted_at', NULL)
            ->get();
    }
}
