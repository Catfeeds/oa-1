@extends('layouts.base')

@section('base')

    <div id="wrapper">

        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                                <span class="block m-t-xs">
                                    <h3>{{ $product->alias }}</h3>
                                </span>
                                <span class="text-muted text-xs block">{{ trans('app.快速导航') }} <b
                                            class="caret"></b></span>
                            </span>
                            </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li>
                                    <a href="{{ route('gmMail', ['pid' => $product->product_id]) }}">{{ trans('gm.邮件列表') }}</a>
                                </li>

                            </ul>
                        </div>
                        <div class="logo-element">
                            {{ $product->name }}
                        </div>
                    </li>

                    <li>
                        <a href="layouts.html"><i class="fa fa-diamond"></i> <span class="nav-label">Layouts</span></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Graphs</span><span
                                    class="fa arrow"></span></a>
                        <ul class="nav nav-second-level collapse">
                            <li><a href="graph_flot.html">Flot Charts</a></li>
                            <li><a href="graph_morris.html">Morris.js Charts</a></li>
                            <li><a href="graph_rickshaw.html">Rickshaw Charts</a></li>
                            <li><a href="graph_chartjs.html">Chart.js</a></li>
                            <li><a href="graph_chartist.html">Chartist</a></li>
                            <li><a href="c3.html">c3 charts</a></li>
                            <li><a href="graph_peity.html">Peity Charts</a></li>
                            <li><a href="graph_sparkline.html">Sparkline Charts</a></li>
                        </ul>
                    </li>

                </ul>
            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i
                                    class="fa fa-bars"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <a href="{{ route('home') }}">
                                <i class="fa fa-home"></i> {{ trans('app.首页') }}
                            </a>
                        </li>


                        <li class="dropdown">
                            <a aria-expanded="false" role="button" href="#" class="dropdown-toggle"
                               data-toggle="dropdown">
                                <i class="fa fa-user"></i> {{ Auth::user()->alias }}
                                <span class="caret"></span>
                            </a>
                            <ul role="menu" class="dropdown-menu">

                                <li>
                                    <a href="{{ route('profile') }}"><i class="fa fa-user"></i> {{ trans('app.我的账号') }}
                                    </a>
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


            <div class="wrapper wrapper-content">
                @yield('content')
            </div>
            @include('layouts.footer')

        </div>
    </div>

@endsection