@extends('layouts.top-nav')

@section('title', trans('首页'))

@push('css')
<style type="text/css">
    #common .ibox {
        margin-bottom: 0;
    }
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
    #common > div, #work_station > div {
        padding: 10px;
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
    @media (min-width: 1200px) {
        .container {
            width: 1300px;
        }
    }

</style>
@endpush

@section('content')
    <meta name="referrer" content="no-referrer"/>
    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row" id="common">
                <div class="col-lg-4">
                    <div class="ibox">
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
                    <div class="ibox m-t-md">
                        <div class="ibox-title">
                            <h2>剩余可申请的福利假</h2>
                        </div>
                        <div class="ibox-content">
                            <p>剩余年假: {{ $remainWelfare['year']['number_day'] ?? '尚未配置该福利假' }}</p>
                            <p>剩余节假日调休: {{ $remainWelfare['change']['number_day'] ?? '尚未配置该福利假' }}</p>
                            <p>剩余探亲假: {{ $remainWelfare['visit']['number_day'] ?? '尚未配置该福利假' }}</p>
                        </div>
                    </div>
                    <div class="ibox m-t-md">
                        <div class="ibox-title">
                            <h2>我的假期申请单</h2>
                        </div>
                        <div class="ibox-content">
                            <p>年假审核中的申请单:</p>
                            <p>审核通过的申请单:</p>
                            <p>需要我审核的申请单:</p>
                        </div>
                    </div>
                    <div class="ibox m-t-md">
                        <div class="ibox-title">
                            <h2>待补齐打卡次数</h2>
                        </div>
                        <div class="ibox-content">
                            <p>上班待补齐打卡次数:</p>
                            <p>下班待补齐打卡次数:</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h2>日程</h2>
                        </div>
                        <div class="ibox-content">
                            <p>2018年11月9日  星期五</p>
                            <p>【狗年】十月初二</p>
                            <p>多云  19°~27</p>
                        </div>
                    </div>
                    <div class="ibox m-t-md">
                        <div class="ibox-title">
                            <h2>审批</h2>
                        </div>
                        <div class="ibox-content">
                            <p>请假|物料</p>
                        </div>
                    </div>
                    <div class="ibox m-t-md">
                        <div class="ibox-title">
                            <h2>人事</h2>
                        </div>
                        <div class="ibox-content">
                            <p></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h2>{{ trans('app.工作日历') }}</h2>
                        </div>
                        <div class="ibox-content no_padding">
                            @include('widget.calendar-user', ['home' => 1])
                        </div>
                    </div>
                    <div class="ibox m-t-md">
                        <div class="ibox-title">
                            <h2>{{ trans('app.公告') }}</h2>
                        </div>
                        <div class="ibox-content no_padding">
                            <div id="content" style=" height: 300px">
                                <div id="bulletContent" class="pre-scrollable" style="padding: 0 15px;" >
                                    {!! $bulletContent->content ?? '' !!}
                                </div>
                            </div>
                        </div>
                    </div>
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
