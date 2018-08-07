@extends('layouts.base')

@section('base')

    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                                <span class="block m-t-xs">
                                    <h3>{{ config('app.name') }}</h3>
                                </span>
                            </span>
                            </a>
                        </div>
                        <div class="logo-element">
                            {{ config('app.nickname') }}
                        </div>
                    </li>

                    {{--考勤菜单--}}
                    @include('attendance.side')

                </ul>
            </div>
        </nav>

        @yield('side-nav')

    </div>

@endsection