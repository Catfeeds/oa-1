@extends('layouts.top-nav')

@section('title', trans('首页'))

@push('css')
<style type="text/css">
    #first-common-row .widget {
        margin-top: 0;
    }

    .pre-scrollable {
        max-height: 100%;
        overflow-y: scroll;
    }

    #common .table {
        margin-bottom: 0;
    }

    #common td {
        vertical-align: middle;
    }

    h2 {
        margin-top: 0;
        margin-bottom: 0;
    }

    .ibox-title {
        padding-top: 5px;
    }

    #work_station .ibox {
        margin-bottom: 0;
    }

    #common > div {
        padding: 0 10px;
    }

    .contact-box {
        margin-bottom: 0;
        border: none;
    }

    .fc-toolbar.fc-header-toolbar {
        margin-top: 1em;
    }

    .no_padding {
        padding: 0;
    }

    @media (min-width: 1400px) {
        .container {
            width: 1300px;
        }
    }
    @media (min-width: 800px ) and (max-width: 1400px) {
        #common > div.col-sm-4 {
            width: 50%;
        }
        .container {width: 100%;}
    }
    @media (max-width: 800px) {
        #common > div.col-sm-4 {
            width: 100%;
        }
        .container {width: 100%;}
    }

</style>
@endpush

@section('content')
    <meta name="referrer" content="no-referrer"/>
    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row" id="common">
                <div class="col-sm-4">
                    @if(Entrust::can('home.common'))
                        <div class="ibox m-b-md">
                            <div class="ibox-title">
                                <h2>{{ trans('app.常用功能') }}</h2>
                            </div>
                            <div class="ibox-content no_padding">
                                <table class="table">
                                    <tr>
                                        <td height="80px">
                                            <a href="{{ route('leave.create', ['id' => \App\Models\Sys\HolidayConfig::LEAVEID]) }}">
                                                <div class="text-center">
                                                    <div>
                                                        <span class="fa fa-calendar-plus-o fa-3x"></span>
                                                    </div>
                                                    <span class="m-t-xs font-bold">{{ trans('app.请假') }}</span>
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('leave.create', ['id' => \App\Models\Sys\HolidayConfig::CHANGE]) }}">
                                                <div class="text-center">
                                                    <div>
                                                        <span class="fa fa-star fa-3x"></span>
                                                    </div>
                                                    <span class="m-t-xs font-bold">{{ trans('app.加班调休') }}</span>
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('daily-detail.info') }}">
                                                <div class="text-center">
                                                    <div>
                                                        <span class="fa fa-sticky-note fa-3x"></span>
                                                    </div>
                                                    <span class="m-t-xs font-bold">{{ trans('app.出勤记录') }}</span>
                                                </div>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if(Entrust::can('home.remain'))
                        <div class="ibox m-b-md">
                            <div class="ibox-title">
                                <h2>剩余可申请的福利假</h2>
                            </div>
                            <div class="ibox-content no_padding">
                                @foreach($remainWelfare as $k => $v)
                                    <p class="list-group-item">{!!  $v['holiday_name'] .' : ' .$v['msg']!!}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if(Entrust::can('home.apply'))
                        <div class="ibox m-b-md">
                            <div class="ibox-title">
                                <h2>我的假期申请单</h2>
                            </div>
                            <div class="ibox-content">
                                <p>审核中的申请单:{{ $apply['apply'][\App\Models\Attendance\Leave::ON_REVIEW] ?? 0 }}</p>
                                <p>审核通过的申请单:{{ $apply['apply'][\App\Models\Attendance\Leave::PASS_REVIEW] ?? 0 }}</p>
                                <p>需要我审核的申请单:{{ $apply['review'] ?? 0 }}</p>
                            </div>
                        </div>
                    @endif
                    @if(Entrust::can('home.recheck'))
                        <div class="ibox m-b-md">
                            <div class="ibox-title">
                                <h2>待补齐打卡次数</h2>
                            </div>
                            <div class="ibox-content">
                                <p>上班待补齐打卡次数:{{ $countRecheck['start'] }}</p>
                                <p>下班待补齐打卡次数:{{ $countRecheck['end'] }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-sm-4">
                    @if(Entrust::can('home.date'))
                        <div class="ibox m-b-md">
                            <div class="ibox-title">
                                <h2>日程</h2>
                            </div>
                            <div class="ibox-content">
                                <p id="day-info-title"></p>
                                <p id="day-info-subtitle"></p>
                                <p>多云 19°~27</p>
                            </div>
                        </div>
                    @endif
                    @if(Entrust::can('home.approve'))
                        <div class="ibox m-b-md">
                            <div class="ibox-title">
                                <h2>审批</h2>
                            </div>
                            <div class="ibox-content no_padding">
                                <div class="tabs-container">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#tab-3"
                                                              aria-expanded="false">请假申请</a>
                                        </li>
                                        <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="true">物料申请</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="tab-3" class="tab-pane active">
                                            <div class="panel-body">
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th>{{ trans('att.请假类型') }}</th>
                                                        <th>{{ trans('att.假期类型') }}</th>
                                                        <th>{{ trans('att.申请人') }}</th>
                                                        <th>{{ trans('att.假期时长') }}</th>
                                                        <th>{{ trans('att.事由') }}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($approve['leave'] as $leave)
                                                        <tr>
                                                            <td>{{ \App\Models\Sys\HolidayConfig::$applyType[\App\Models\Sys\HolidayConfig::getHolidayApplyList()[$leave['holiday_id']]] }}</td>
                                                            <td>{{ \App\Models\Sys\HolidayConfig::holidayList()[$leave['holiday_id']] }}</td>
                                                            <td>{{ \App\User::getAliasList()[$leave['user_id']] ?? '' }}</td>
                                                            <td>
                                                                {{\App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($leave['holiday_id'], $leave['start_time'], $leave['start_id'], $leave['number_day'])['number_day']}}
                                                            </td>
                                                            <td>{{ $leave['reason'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div id="tab-4" class="tab-pane">
                                            <div class="panel-body">
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th>{{ trans('material.类型') }}</th>
                                                        <th>{{ trans('material.名称') }}</th>
                                                        <th>{{ trans('material.借用事由') }}</th>
                                                        <th>{{ trans('material.预计归还时间') }}</th>
                                                        <th>{{ trans('att.申请人') }}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($approve['material'] as $material)
                                                        <tr>
                                                            <td>{{ $material['inventory_type'] }}</td>
                                                            <td>{{ $material['inventory_name'] }}</td>
                                                            <td>{{ $material['reason'] }}</td>
                                                            <td>{{ $material['expect_return_time'] }}</td>
                                                            <td>{{ \App\User::getUserAliasToId($material['user_id'])->alias }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(Entrust::can('home.personnel'))
                        <div class="ibox m-b-md">
                            <div class="ibox-title">
                                <h2>人事</h2>
                            </div>
                            <div class="ibox-content">
                                <p>今日</p>
                                <p id="day-info-content"></p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-sm-4">
                    @if(Entrust::can('home.calendar'))
                        <div class="ibox m-b-md">
                            <div class="ibox-title">
                                <h2>{{ trans('app.工作日历') }}</h2>
                            </div>
                            <div class="ibox-content no_padding">
                                @include('widget.calendar-user', ['clickRead' => 1])
                            </div>
                        </div>
                    @endif
                    @if(Entrust::can('home.bulletin'))
                        <div class="ibox m-b-md">
                            <div class="ibox-title">
                                <h2>{{ trans('app.公告') }}</h2>
                            </div>
                            <div class="ibox-content no_padding">
                                <div id="content" style=" height: 300px">
                                    <div id="bulletContent" class="pre-scrollable no_padding">
                                        <ul class="list-group">
                                            @foreach($bullets as $bullet)
                                                <a href="{{ route('bulletin.show', ['id' => $bullet->id]) }}" class="list-group-item">
                                                    {{ $bullet->title }}
                                                    <span style="float: right">
                                                {{ \App\Components\Helper\DataHelper::timeDiff($bullet->created_at, date('Y-m-d H:i:s')).'发布' }}
                                                    </span>
                                                </a>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            {{--<div class="row" id="work_station">
                <div class="col-md-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h2>{{ trans('app.工作台') }}</h2>
                        </div>
                        <div class="ibox-content no_padding">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="col-md-2">
                                        <div class="contact-box">
                                            <a href="{{route('attIndex')}}">
                                                <div class="col-sm-4">
                                                    <div class="text-center">
                                                        <img alt="image" class="img-circle m-t-xs img-responsive"
                                                             src="img/att.png">
                                                        <div class="m-t-xs font-bold">考勤系统</div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <h3><strong>考勤系统后台</strong></h3>
                                                    <p><i class="fa fa-map-marker"></i>考勤相关：请假、打卡等</p>
                                                </div>
                                                <div class="clearfix"></div>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="col-md-2">
                                        <div class="contact-box">
                                            <a href="{{ route('manage.index') }}">
                                                <div class="col-sm-4">
                                                    <div class="text-center">
                                                        <img alt="image" class="img-circle m-t-xs img-responsive"
                                                             src="img/hr.png">
                                                        <div class="m-t-xs font-bold">人事系统</div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <h3><strong>人事系统管理</strong></h3>
                                                    <p><i class="fa fa-map-marker"></i> 人事相关：招聘，培训等</p>
                                                </div>
                                                <div class="clearfix"></div>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="col-md-2">
                                        <div class="contact-box">
                                            <a href="{{route('CrmIndex')}}">
                                                <div class="col-sm-4">
                                                    <div class="text-center">
                                                        <img alt="image" class="img-circle m-t-xs img-responsive"
                                                             src="img/crm.png">
                                                        <div class="m-t-xs font-bold">CRM系统</div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <h3><strong>CRM系统管理</strong></h3>
                                                    <p><i class="fa fa-map-marker"></i> CRM：对账，客户管理</p>
                                                    --}}{{--<address>
                                                        <strong></strong><br>
                                                        <br>
                                                        <br>
                                                        <abbr></abbr>
                                                    </address>--}}{{--
                                                </div>
                                                <div class="clearfix"></div>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="col-md-4"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>--}}
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('ueditor/ueditor.parse.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.contact-box').each(function () {
            animationHover(this, 'pulse');
        });

//        $('/*.table:first, */#content').css('height', window.screen.height * 0.5);
    });
</script>
@endpush
