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
                        @if(Entrust::can(['leave*', 'staff*']))
                        <li>
                            <a aria-expanded="false" role="button" href="{{route('attIndex')}}">{{ trans('app.考勤系统') }}</a>
                        </li>
                        @endif
                        @if(Entrust::can(['holiday-config', 'approval-step', 'punch-rules', 'calendar']))
                        <li>
                            <a aria-expanded="false" role="button" href="{{ route('holiday-config') }}">{{ trans('staff.系统配置') }}</a>
                        </li>
                        @endif
                        @if(Entrust::can(['staff*', 'entry*']))
                        <li>
                            <a aria-expanded="false" role="button" href="{{ route('manage.index') }}">{{ trans('app.员工管理') }}</a>
                        </li>
                        @endif
                        @if(Entrust::can(['material.approve*', 'material.apply*']))
                        <li>
                            <a aria-expanded="false" role="button" href="{{ route('material.apply.index') }}">{{ trans('app.物料管理') }}</a>
                        </li>
                        @endif
                        <li>
                            <a aria-expanded="false" role="button" href="{{ route('CrmIndex') }}">{{ trans('app.CRM系统') }}</a>
                        </li>

                        @if(Entrust::can(['user']))
                        <li @if(Route::is('user*')) class="active" @endif>
                            <a aria-expanded="false" role="button" href="{{ route('user') }}">{{ trans('app.账号管理') }}</a>
                        </li>
                        @endif

                        @if(Entrust::can(['role']))
                            <li @if(Route::is('role*')) class="active" @endif>
                                <a aria-expanded="false" role="button" href="{{ route('role') }}">{{ trans('app.权限管理') }}</a>
                            </li>
                        @endif
                    </ul>

                    <ul class="nav navbar-nav navbar-right">

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
