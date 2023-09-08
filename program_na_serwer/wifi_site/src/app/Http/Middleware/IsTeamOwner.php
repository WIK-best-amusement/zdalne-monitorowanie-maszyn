<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class IsTeamOwner
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

        $team = Team::getUserTeamByUserId(Auth::user()->id);
        if ($team->id != Session::get('teamId')) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
