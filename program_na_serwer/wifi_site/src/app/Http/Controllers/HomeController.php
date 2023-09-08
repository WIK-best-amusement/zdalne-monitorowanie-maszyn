<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceProblem;
use App\Models\Team;
use App\Models\TeamsUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $members = TeamsUser::getTeamMembers(Session::get('teamId'));
        $userTeams = Team::getTeamsWhereUserBelongsTo(Auth::user()->id);

        if (\Session::get('teamOwner')) {
            $deviceProblems = DeviceProblem::getErrorsForTeamOwner();
            $deviceProblemsByDay = DeviceProblem::getGroupedErrorsForTeamOwner();
        } else {
            $deviceProblems = DeviceProblem::getErrorsForTeamMember(Auth::user()->id);
            $deviceProblemsByDay = DeviceProblem::getGroupedErrorsForTeamMember(Auth::user()->id);
        }


        return view('dashboard', ['pageTitle' => 'Dashboard for '.Session::get('teamName'), 'members' => $members, 'deviceProblems' => $deviceProblems, 'userTeams' => $userTeams, 'deviceProblemsByDay' => $deviceProblemsByDay]);
    }
}
