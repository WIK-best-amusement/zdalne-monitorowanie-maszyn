<?php

namespace App\Http\Middleware;

use App\Models\Location;
use App\Models\Team;
use App\Teams;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;

class UserGroups
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        \Barryvdh\Debugbar\Facade::addMessage('UserGroups Middleware '.Auth::user()->id);

        view()->share('teamsWhereUserBelongsTo', Team::getTeamsWhereUserBelongsTo(Auth::user()->id));

        if (\Session::get('teamOwner')) {
            $locations = Location::getLocationsByTeam(\Session::get('teamId'));
        } else {
            $locations = Location::select('locations.id', 'locations.name')
                ->join('locations_users', 'locations.id', '=', 'locations_users.location_id')
                ->where('team_id', \Session::get('teamId'))
                ->where('user_id', Auth::user()->id)
                ->get();
        }

        view()->share('locationsInvitedTo', $locations);

        $res = DB::table('users_roles')->where('user_id', '=', Auth::user()->id)->first();
        empty($res) ? $role = 'user' : $role = $res->role;

        Session::put('role', $role);
        return $next($request);
    }
}
