@extends('layouts.auth')

@section('content')
    <div class="user-action-box-body">
        <p class="user-action-box-msg">Register</p>
        <form role="form" method="POST" action="{{ url('/register') }}">
            {{ csrf_field() }}

            <div class="form-group has-feedback{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">Full name</label>
                <input id="name" name="name" type="text" class="form-control" placeholder="Full name" value="{{ old('name') }}"
                       required autofocus>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email">E-mail</label>
                <input id="email" name="email" type="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" class="form-control" placeholder="Password" value="{{ old('password') }}" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group has-feedback{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <label for="password_confirmation">Retype password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Retype password" value="{{ old('password_confirmation') }}" required>
                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                @endif
            </div>



            <div class="row">
                <div class="col-xs-8">
                    @if (1 == 2)
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox"> I agree to the <a href="#">terms</a>
                        </label>
                    </div>
                    @endif
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
                </div>
            </div>

            <a href="{{ url('login') }}" class="text-center">I already have a membership</a>
        </form>
    </div>
@endsection
