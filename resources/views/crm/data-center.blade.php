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

                    {{--对账菜单--}}
                    @if(Entrust::can(['crm-all', 'reconciliation-all']))
                        <li @if (Route::is('reconciliation*')) class="active" @endif >
                            <a href="#"><i class="fa fa-newspaper-o"></i> <span
                                        class="nav-label">{{ trans('crm.对账管理') }}</span><span
                                        class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit']))
                                    <li @if (Route::is('reconciliationAudit*') ) class="active" @endif>
                                        <a href="{{ route('reconciliationAudit') }}">{{ trans('crm.对账审核') }}</a>
                                    </li>
                                @endif
                                @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationProduct']))
                                    <li @if (Route::is('reconciliationProduct*') ) class="active" @endif>
                                        <a href="{{ route('reconciliationProduct') }}">{{ trans('crm.游戏列表') }}</a>
                                    </li>
                                @endif
                                @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationPrincipal']))
                                    <li @if (Route::is('reconciliationPrincipal*') ) class="active" @endif>
                                        <a href="{{ route('reconciliationPrincipal') }}">{{ trans('crm.对账负责人管理') }}</a>
                                    </li>
                                @endif
                                @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationDifferenceType']))
                                    <li @if (Route::is('reconciliationDifferenceType*') ) class="active" @endif>
                                        <a href="{{ route('reconciliationDifferenceType') }}">{{ trans('crm.差异类管理') }}</a>
                                    </li>
                                @endif
                                @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationProportion']))
                                    <li @if (Route::is('reconciliationProportion*') ) class="active" @endif>
                                        <a href="{{ route('reconciliationProportion') }}">{{ trans('crm.分成比例管理') }}</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                </ul>
            </div>
        </nav>

        @yield('side-nav')

    </div>

@endsection