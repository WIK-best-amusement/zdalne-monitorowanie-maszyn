<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class DeviceProblem
 * 
 * @property int $id
 * @property int $device_id
 * @property string $title
 * @property string $description
 * @property Carbon $date
 * @property int $displayed
 * 
 * @property Device $device
 *
 * @package App\Models
 */
class DeviceProblem extends Model
{
	protected $table = 'device_problems';
	public $timestamps = false;

	protected $casts = [
		'device_id' => 'int',
		'displayed' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'device_id',
		'title',
		'description',
		'date',
		'displayed'
	];

	public function device()
	{
		return $this->belongsTo(Device::class);
	}

    public static function getErrorsForTeamOwner()
    {
        return Device::select('devices.id', 'devices.name', 'device_problems.id', 'device_problems.title', 'device_problems.description', 'device_problems.date', 'device_problems.displayed')
            ->join('device_problems', 'device_problems.device_id', '=', 'devices.id')
            ->where('devices.team_id', \Session::get('teamId'))
            ->where('displayed', 1)
            ->orderBy('date', 'desc')
            ->get();
    }

    public static function getGroupedErrorsForTeamOwner()
    {
        return Device::select(DB::RAW('COUNT(device_problems.id) as count'), DB::RAW("(DATE_FORMAT(device_problems.date, '%Y-%m-%d')) as day"))
            ->join('device_problems', 'device_problems.device_id', '=', 'devices.id')
            ->where('devices.team_id', \Session::get('teamId'))
            ->groupBy(DB::RAW("(DATE_FORMAT(device_problems.date, '%Y-%m-%d'))"))
            ->orderBy(DB::RAW("(DATE_FORMAT(device_problems.date, '%Y-%m-%d'))"), 'desc')
            ->get();
    }

    public static function getErrorsForTeamMember($userId)
    {
        $teamId = \Session::get('teamId');
        return Device::select('devices.id', 'devices.name', 'device_problems.id', 'device_problems.title', 'device_problems.description', 'device_problems.date', 'device_problems.displayed')
            ->join('locations_devices', 'locations_devices.device_id', '=', 'devices.id')
            ->join('locations_users', 'locations_users.location_id', '=', 'locations_devices.location_id')
            ->join('locations', function ($join)  use ($teamId) {
                $join->on('locations.id', '=', 'locations_devices.location_id')
                    ->where('locations.team_id', '=', $teamId);
            })
            ->join('device_problems', 'device_problems.device_id', '=', 'devices.id')
            ->where('locations_users.user_id', $userId)
            ->where('devices.team_id', \Session::get('teamId'))
            ->get();
    }

    public static function getGroupedErrorsForTeamMember($userId)
    {
        $teamId = \Session::get('teamId');
        return Device::select(DB::RAW('COUNT(device_problems.id) as count'), DB::RAW("(DATE_FORMAT(device_problems.date, '%Y-%m-%d')) as day"))
            ->join('locations_devices', 'locations_devices.device_id', '=', 'devices.id')
            ->join('locations_users', 'locations_users.location_id', '=', 'locations_devices.location_id')
            ->join('locations', function ($join)  use ($teamId) {
                $join->on('locations.id', '=', 'locations_devices.location_id')
                    ->where('locations.team_id', '=', $teamId);
            })
            ->join('device_problems', 'device_problems.device_id', '=', 'devices.id')
            ->where('locations_users.user_id', $userId)
            ->where('devices.team_id', \Session::get('teamId'))
            ->groupBy(DB::RAW("(DATE_FORMAT(device_problems.date, '%Y-%m-%d'))"))
            ->orderBy(DB::RAW("(DATE_FORMAT(device_problems.date, '%Y-%m-%d'))"), 'desc')
            ->get();
    }
}
