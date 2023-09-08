<?php

namespace App\Http\Controllers;

use App\Mail\Invitation;
use App\Models\Location;
use App\Models\LocationsRole;
use App\Models\LocationsSettlements;
use App\Models\LocationsUser;
use App\Models\LocationsUsersProfits;
use App\Models\Option;
use App\Models\OptionsGroupsRole;
use App\Models\Team;
use App\Models\TeamsUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class TeamController extends Controller
{
    const STAFF = 1;
    const TENANTRY = 2;
    const TECHNICIAN = 3;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show user list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $members = TeamsUser::getTeamMembers(Session::get('teamId'));
        return view('team.list', array('pageTitle' => 'Members', 'members' => $members));
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function switch($teamId)
    {
        $isOwner = false;

        $teamIdsWhereUserBelongsTo = Team::getIdsOfTeamWhereUserBelongsTo(Auth::user()->id);
        $userTeam = Team::getUserTeamByUserId(Auth::user()->id);

        if (!in_array($teamId, $teamIdsWhereUserBelongsTo)) {
            $teamId = $userTeam->id;
        }

        if ($teamId == $userTeam->id) {
            $isOwner = true;
        }

        $team = Team::find($teamId);
        Session::put('teamId', $teamId);
        Session::put('teamOwner', $isOwner);
        Session::put('teamName', $team->name);

        return redirect()->route('home');
    }

    /**
     * invite user.
     *
     * @return \Illuminate\Http\Response
     */
    public function invite()
    {
        $fieldsNumber = old('name') ? count(old('name')) : 1;

        return view('team.invite', ['pageTitle' => 'Invite user', 'fieldsNumber' => $fieldsNumber]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inviteSend(Request $request)
    {
        $this->validate($request, $this->inviteSendRules($request));
        $team = Team::getUserTeamByUserId(Auth::user()->id);

        foreach ($request->email as $key => $email) {
            $password = '';
            $newUser = false;
            $user = User::getUserByEmail($email);

            if ($user->exists()) {
                $userId = $user->value('id');
            } else {
                $newUser = true;
                $password = str_random(8);

                $user = User::createUser($request->name[$key], $email, $password, 'invitation');
                $userId = $user->id;
            }

            Team::addUserToTeam($userId, $team->id);
            Mail::to($email)->send(new Invitation($request->name[$key], $newUser, $password));
        }

        Session::flash('submited-form', true);
        return back();
    }

    /**
     * @param $request
     * @return array
     */
    private function inviteSendRules($request)
    {
        $rules = [];

        foreach ($request->get('name') as $key => $val) {
            $rules['name.' . $key] = 'required|min:3|max:255';
        }

        foreach ($request->get('email') as $key => $val) {
            $rules['email.' . $key] = 'required|email|max:255';
        }

        return $rules;
    }

    public function locations()
    {
        $teamId = Session::get('teamId');

        $usersWithLocation = LocationsUser::getUsersWithLocation($teamId);
        $members = TeamsUser::getTeamMembers($teamId);
        foreach ($members as $key => $member) {
            if ($member->id === Auth::user()->id) {
                unset($members[$key]);
                break;
            }
        }

        $locations = Location::getLocationsByTeam($teamId);
        $settlements = LocationsSettlements::getUsersWithSettlements($teamId);
        $locationsIds = [];
        foreach ($locations as $location) {
            array_push($locationsIds, $location->id);
        }

        $profits = LocationsUsersProfits::getUsersWithProfits($locationsIds);

        return view('team.locations', array('pageTitle' => 'Locations', 'locations' => $locations, 'members' => $members, 'usersWithLocation' => $usersWithLocation, 'settlements' => $settlements, 'profits' => $profits));
    }

    public function addNewLocation(Request $request)
    {

        if (!empty($request->get('locationName'))) {
            $locationId = Location::insertGetId(
                ['name' => $request->get('locationName'), 'team_id' => Session::get('teamId')]
            );
            LocationsUsersProfits::insert(
                ['location_id' => $locationId,
                    'user_id' => Auth::user()->id,
                    'profit' => 100,
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()]
            );
        }
        return back();
    }

    public function editLocation(Request $request)
    {
        //TODO: csrf_field
        $locationId = $request->get('location-id');

        //TODO: error message
        if (!empty($request->get('name')) && array_sum($request->profit_value) == 100) {
            Location::updateLocationsName($locationId, $request->get('name'));
            LocationsUser::deleteByLocation($locationId);

            $this->updateTeamLocationWithUsers($request->get('technician'), $locationId, self::TECHNICIAN);
            $this->updateTeamLocationWithUsers($request->get('tenantry'), $locationId, self::TENANTRY);
            $this->updateTeamLocationWithUsers($request->get('staff'), $locationId, self::STAFF);

            LocationsSettlements::deleteByLocation($locationId);

            $this->updateSettlements($request->get('settlement'), $locationId);

            $profits = [];
            $time = \Carbon\Carbon::now()->toDateTimeString();
            foreach ($request->profit_value as $key => $value) {
                if (!key_exists($key, $request->profit_distribution)) {
                    return back();
                }

                if ($request->profit_distribution[$key] == '' || $value == '') {
                    return back();
                }

                $profits[] = ['location_id' => $locationId,
                    'user_id' => $request->profit_distribution[$key],
                    'profit' => $value,
                    'created_at' => $time,
                    'updated_at' => $time];
            }

            LocationsUsersProfits::where(['location_id' => $locationId])->delete();
            LocationsUsersProfits::insert($profits);

            $this->updateProfits($locationId, $request->profit_value, $request->profit_distribution);

        }
        return back();
    }

    public function deleteLocation($id)
    {
        Location::deleteLocation($id);
        LocationsUsersProfits::where(['location_id' => $id])->delete();
        return back();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function updateTeamLocationWithUsers($field, $locationId, $type)
    {
        if ($field && is_array($field)) {
            foreach ($field as $item) {
                LocationsUser::insert(
                    ['user_id' => $item, 'role' => $type, 'location_id' => $locationId]
                );
            }
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function updateSettlements($users, $locationId)
    {
        if ($users && is_array($users)) {
            foreach ($users as $user) {
                LocationsSettlements::insert(
                    ['user_id' => $user, 'location_id' => $locationId]
                );
            }
        }
    }

    private function updateProfits($locationId, $values, $users) {
        $profits = [];
        $time = \Carbon\Carbon::now()->toDateTimeString();
        foreach ($values as $key => $value) {
            if (!key_exists($key, $users)) {
                return back();
            }

            $profits[] = ['location_id' => $locationId,
                'user_id' => $users[$key],
                'profit' => $value,
                'created_at' => $time,
                'updated_at' => $time];
        }

        LocationsUsersProfits::where(['location_id' => $locationId])->delete();
        LocationsUsersProfits::insert($profits);
    }

    public function locationsRolesPermissions()
    {
        if (Session::get('role') != 'admin') {
            return redirect()->route('home')->with('dontHavePermission', 'You don`t have permissions');
        }

        $roles = LocationsRole::orderBy('sort')->get();
        $options = Option::get();
        $optionsWithRoles = OptionsGroupsRole::getOptionsWithRolesAsArray();

        return view('team.locations_roles_permissions', array('pageTitle' => 'Location roles permissions', 'optionsList' => $options, 'roles' => $roles, 'canSeeOptions' => $optionsWithRoles));
    }

    public function addLocationRolesPermissions(Request $request)
    {
        if (Session::get('role') != 'admin') {
            return redirect()->route('home')->with('dontHavePermission', 'You don`t have permissions');
        }

        $options = $request->get('option');
        OptionsGroupsRole::insertNew($options);
        return back();
    }

    public function removeUserFromTeam($userId) {
        DB::transaction(function () use ($userId) {
            $locations = Location::where('team_id', Session::get('teamId'))->pluck('id')->toArray();

            TeamsUser::where(['user_id' => $userId, 'team_id' => Session::get('teamId')])->delete();

            LocationsUser::where(['user_id' => $userId])->whereIn('location_id', $locations)->delete();
            LocationsUsersProfits::where(['user_id' => $userId])->whereIn('location_id', $locations)->delete();
            LocationsSettlements::where(['user_id' => $userId])->whereIn('location_id', $locations)->delete();
        });
        return back();
    }
}
