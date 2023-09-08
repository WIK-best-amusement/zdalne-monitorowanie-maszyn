<?php

namespace App\Http\Middleware;

use App\GroupsUsers;
use App\Models\LocationsUser;
use App\Models\Team;
use App\Teams;
use Closure;
use Illuminate\Support\Facades\Auth;

class CanSeeDevice
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $deviceId = (int)$request->deviceId;

        $canSee = true;
        if (\Session::get('teamOwner')) {
            if(Team::isDeviceInUserTeam(Auth::user()->id, $deviceId) == null) {
                $canSee = false;
            }
        } else {
            if (LocationsUser::isDeviceInUserLocation(Auth::user()->id, $deviceId) == null) {
                $canSee = false;
            }
        }

        if ($canSee == false) {

            if ($request->response !== null && $request->response === 'json') {
                return response()->json(['response' => 'Permission problem'])->setStatusCode(500);
            }
            return redirect()->route('home')->with('dontHavePermission', 'You don`t have permission to access this device');
        }

        return $next($request);
    }
}
