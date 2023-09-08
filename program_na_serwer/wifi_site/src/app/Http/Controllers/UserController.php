<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;

class UserController extends Controller
{
    use ResetsPasswords;

    public function profile()
    {
        return view('user.profile', array('pageTitle' => 'User profile', 'user' => Auth::user()));
    }

    public function updateDetails(Request $request)
    {
        $err = $this->validateName($request->all());
        if ($err->fails()) {
            return redirect()->route('show-user-profile')->withErrors($err);
        }

        User::updateUserName(Auth::user()->id, $request->get('name'));

        $request->session()->flash('status', 'Name was updated!');
        return redirect()->route('show-user-profile');
    }

    public function passwordUpdate(Request $request)
    {
        $err = $this->validatePassword($request->all());
        if ($err->fails()) {
            return redirect()->route('show-user-profile')->withErrors($err);
        }
        $this->resetPassword(Auth::user(), $request->get('password'));
        $request->session()->flash('status', 'Password was chaned!');
        return redirect()->route('show-user-profile');
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();

        $this->guard()->login($user);
    }

    protected function validateName($data)
    {
        return Validator::make($data, [
            'name' => 'required|max:252'
        ]);
    }

    protected function validatePassword(array $data)
    {
        return Validator::make($data, [
            'password' => 'required|min:6|confirmed'
        ]);
    }

    public static function setUserSessionData()
    {
        $team = Team::getUserTeamByUserId(Auth::user()->id);
        Session::put('teamId', $team->id);
        Session::put('teamOwner', true);
        Session::put('teamName', $team->name);
        Session::put('teamHash', $team->hash);
    }
}
