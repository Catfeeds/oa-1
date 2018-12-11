@extends('attendance.data-center')

@section('side-nav')

    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                </div>

                <ul class="nav navbar-top-links navbar-right">

                    <li>
                        <a href="{{ route('home') }}">
                            <i class="fa fa-th-large"></i> <span class="nav-label">{{ trans('app.首页') }}</span>
                        </a>
                    </li>

                    @if(Entrust::can(['holiday-config*', 'approval-step*', 'punch-rule*', 'calendar*']))
                    <li>
                        <a href="{{ route('holiday-config') }}">
                            <i class="glyphicon glyphicon-cog"></i> <span class="nav-label">{{ trans('app.系统配置') }}</span>
                        </a>
                    </li>
                    @endif

                    <li class="dropdown">
                        <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i> {{ Auth::user()->alias }}
                            <span class="caret"></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">

                            <li>
                                <a href="{{ route('profile') }}"><i class="fa fa-user"></i> {{ trans('app.个人信息') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                               document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out"></i> {{ trans('app.登出') . ' (' . Auth::user()->alias . ')' }}
                                    {!! Form::open(['url' => route('logout'), 'id' => 'logout-form']) !!}
                                    {!! Form::close() !!}
                                </a>
                            </li>

                        </ul>
                    </li>
                </ul>

            </nav>
        </div>
        <div class="row wrapper border-bottom white-bg page-heading">

            @section('page-head')

                <div class="col-sm-4 tooltip-demo">
                    <h2>
                        {{ $title }}
                        {!! BaseHtml::about(Route::currentRouteName()) !!}
                    </h2>
                </div>
            @show

        </div>

        <div class="wrapper wrapper-content animated fadeInRight">

            @yield('content')

        </div>

        @include('layouts.footer')

    </div>

@endsection
