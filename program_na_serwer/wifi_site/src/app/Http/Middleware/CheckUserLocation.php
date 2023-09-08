<?php

namespace App\Http\Middleware;

use App\Models\Location;
use App\Models\LocationsUser;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserLocation
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
        $locationId = (int)$request->locationId;

        if (!LocationsUser::isUserInLocation($locationId, Auth::user()->id) && !Location::isUserOwnerOfLocation($locationId, Auth::user()->id)) {
            return redirect()->route('home')->with('dontHavePermission', 'You don`t have permission');

        }

        return $next($request);
    }
}
