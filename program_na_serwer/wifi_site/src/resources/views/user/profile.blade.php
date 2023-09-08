@extends('layouts.app')

@section('content')


    <div class="row">

        @if (Session::get('status'))
            <div class="col-md-12">
                <div class="callout callout-success">
                    <h4>Success!</h4>
                    <p>{{ Session::get('status') }}</p>
                </div>
            </div>
        @endif

        @if (count($errors) > 0)
            <div class="col-md-12">
                <div class="callout callout-danger">
                    <h4>Wrong data</h4>
                    <p>There is a problem that we need to fix.</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle" src="{{ Gravatar::src($user->email, 128) }}"
                         alt="User profile picture">
                    <h3 class="profile-username text-center">{{ $user->name }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2 class="page-header">Name change</h2>
                        </div>
                    </div>

                    <form class="form-horizontal" method="post" action="{{ URL::route('updateDetails') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">Name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputName" name="name" placeholder="Name"
                                       value="{{ $user->name }}">
                                <input name="update-name" type="hidden" value="true"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">E-mail</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputName" name="name" placeholder="Name" disabled
                                       value="{{ $user->email }}">
                                <input name="update-name" type="hidden" value="true"/>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" value="name-change" class="btn btn-danger">Submit</button>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-xs-12">
                            <h2 class="page-header">Password change</h2>
                        </div>
                    </div>

                    <form class="form-horizontal" method="post" action="{{ URL::route('passwordUpdate') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="password" class="col-sm-2 control-label">Password</label>

                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="password" name="password" value="" autocomplete="off"
                                       placeholder="New password">
                                <input name="update-password" type="hidden" value="true"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="col-sm-2 control-label">Repeat password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="password_confirmation" value="" autocomplete="off"
                                       name="password_confirmation" placeholder="Repeat new password">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" value="password-change" class="btn btn-danger">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                </div>
            </div>

@endsection
