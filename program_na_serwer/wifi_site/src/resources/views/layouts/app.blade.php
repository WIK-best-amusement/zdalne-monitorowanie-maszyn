<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name', 'WIK WiFi') }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ URL::asset('plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ URL::asset('css/AdminLTE.css?t=1') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/_all-skins.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('plugins/pace/pace.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('plugins/datatables/dataTables.bootstrap.css') }}">
    @yield('view.css')

    <script src="{{ URL::asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <a href="{{ url('/') }}" class="logo">
            <span class="logo-mini"><strong>A</strong>LT</span>
            <span class="logo-lg">{{ config('app.name', 'WIK WiFi') }}</span>
        </a>

        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">

                    @if (count($teamsWhereUserBelongsTo) > 1)
                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="fa fa-plus-square-o"></span>
                                <span class="label label-warning">{{ count($teamsWhereUserBelongsTo) }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You are asigned into {{ count($teamsWhereUserBelongsTo) }} teams</li>
                                <li>
                                    <ul class="menu">
                                        @foreach($teamsWhereUserBelongsTo as $team)
                                            <li>
                                                <a href="{{ URL::route('change-user-team', $team->id) }}">
                                                    <div class="pull-left">
                                                        <img alt="User Gravatar" class="img-circle"
                                                             src="{{ Gravatar::src($team->name) }}">
                                                    </div>
                                                    <strong>{{ $team->name }}</strong>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    @endif
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img alt="User Gravatar" class="user-image" src="{{ Gravatar::src(Auth::user()->email) }}">
                            <span class="hidden-xs">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <img alt="User Gravatar" src="{{ Gravatar::src(Auth::user()->email) }}"
                                     class="img-circle" alt="User Image">
                                <p>
                                    {{ Auth::user()->name }}
                                    <small>Member since {{Auth::user()->created_at}}</small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{ URL::route('show-user-profile') }}" class="btn btn-primary btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <form action="{{ url('/logout') }}" method="POST">
                                        {!! csrf_field() !!}
                                        <button type="submit" class="btn btn-danger btn-flat">Sign out</button>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

        </nav>
    </header>
    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{ Gravatar::src(Auth::user()->email) }}" class="img-circle" alt="User Gravatar">
                </div>
                <div class="pull-left info">
                    <p>{{ Auth::user()->name }}</p>
                    Team ID: {{Session::get('teamHash')}}
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="header">MAIN NAVIGATION</li>
                @if(Session::get('teamOwner'))
                    <li {{ (Request::is('team/list') ? ' class=active' : '') }}>
                        <a href="{{ url('/team/list') }}"><span class="fa fa-users"></span> Users List</a>
                    </li>

                    <li{{ (Request::is('team/invite') ? ' class=active' : '') }}>
                        <a href="{{ url('/team/invite') }}"><span class="fa fa-envelope"></span> Invite User</a>
                    </li>

                    @if(\Illuminate\Support\Facades\Session::get('role') === 'admin')
                        <li {{ (Request::is('team/locations_roles') ? ' class=active' : '') }}>
                            <a href="{{ url('/team/locations_roles') }}"><span class="fa fa-users"></span>Location roles</a>
                        </li>
                    @endif
                @endif

                <li{{ (Request::is('devices') || Request::is('devices/details/*') ? ' class=active' : '') }}>
                    <a href="{{ url('/devices') }}"><span class="fa fa-wifi"></span>Devices</a>
                </li>


                <li{{ (Request::is('team/locations') ? ' class=active' : '') }}>
                    @if(Session::get('teamOwner'))
                        <a href="{{ url('/team/locations') }}"><span class="fa fa-folder"></span>Locations</a>
                    @else
                        <a role="button" class="no-href"><span class="fa fa-folder"></span>Locations</a>
                    @endif

                    <ul class="treeview-menu">
                        @foreach ($locationsInvitedTo as $item)
                            <li{{ (Request::is('/devices/location/'.$item->id) ? ' class=active' : '') }}>
                                <a class="location-name" title="{{ $item->name }}"
                                   href="{{ url('/devices/location/'.$item->id) }}"><span
                                            class="fa fa-folder"></span>{{ $item->name }}</a>
                                <a class="report" href="{{ url('/reports/location/'.$item->id.'/counters') }}"><span
                                            class="fa fa-table"></span></a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        </section>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <h3>{{ $pageTitle }}</h3>
            <ol class="breadcrumb">
                <li><a href="{{ url('') }}"><span class="fa fa-dashboard"></span> Home</a></li>
                <li class="active">{{ $pageTitle }}</li>
            </ol>
        </section>

        <section class="content">
            @yield('content')
        </section>
    </div>

    <footer class="main-footer">
        <img src="{{ URL::asset('img/footer.png') }}" alt="The institutions of the European Union"/>
    </footer>

</div>
<script src="{{ URL::asset('plugins/js-cookie/js.cookie.js') }}"></script>
<script src="{{ URL::asset('plugins/jQueryUI/jquery-ui.js') }}"></script>
<script src="{{ URL::asset('bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('plugins/fastclick/fastclick.js') }}"></script>
<script src="{{ elixir('js/theme.js') }}"></script>
<script src="{{ URL::asset('plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ elixir('plugins/accessibility/accessibility.min.js') }}"></script>
<script src="{{ elixir('js/app.js') }}"></script>
<script src="{{ elixir('js/toggles.min.js') }}"></script>

@yield('view.scripts')

</body>
</html>
