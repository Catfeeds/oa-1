@extends('layouts.base')

@section('body-class', 'top-navigation')

@section('base')

<div id="wrapper">
    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom white-bg">
            <nav class="navbar navbar-static-top" role="navigation">
                <div class="navbar-header">
                    <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                        <i class="fa fa-reorder"></i>
                    </button>
                    <a href="{{ route('home') }}" class="navbar-brand"> {{ trans(config('app.name')) }} </a>
                </div>
                <div class="navbar-collapse collapse" id="navbar">
                    <ul class="nav navbar-nav">
                        <li>
                            <a aria-expanded="false" role="button" href="{{ route('home') }}">{{ trans('app.首页') }}</a>
                        </li>

                        @if(Entrust::can(['user-all', 'user']))
                        <li @if(Route::is('user*')) class="active" @endif>
                            <a aria-expanded="false" role="button" href="{{ route('user') }}">{{ trans('app.员工管理') }}</a>
                        </li>
                        @endif

                        @if(Entrust::can(['role-all', 'role']))
                        <li @if(Route::is('role*')) class="active" @endif>
                            <a aria-expanded="false" role="button" href="{{ route('role') }}">{{ trans('app.职务管理') }}</a>
                        </li>
                        @endif

                        @if(Entrust::can(['job-all', 'job', 'dept-all', 'dept', 'school-all', 'school',
                        'holiday-config-all', 'holiday-config','approval-step-all', 'approval-step', 'punch-rules-all', 'punch-rules']))
                            <li @if(Route::is(['job*', 'dept*', 'school*', 'holiday-config' , 'approval-step*', 'punch-rules*', 'calendar*'])) class="active" @endif>
                                <a aria-expanded="false" role="button" href="{{ route('dept') }}">{{ trans('app.系统配置') }}</a>
                            </li>
                        @endif

                    </ul>

                    <ul class="nav navbar-nav navbar-right">

                        <li class="dropdown">
                            @if(!\App\Models\UserExt::checkIsConfirm(Auth::user()->user_id))
                                <a class="dropdown-toggle count-info"
                                   href="{{ route('profile.confirmEdit') }}">
                                    <i class="fa fa-warning"></i>
                                        <span class="label red-bg label-warning">{{ trans('app.信息待完善') }}</span>
                                </a>
                            @endif
                        </li>

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
                </div>
            </nav>
        </div>

        @yield('content')

        @include('layouts.footer')

    </div>
</div>

@endsection
