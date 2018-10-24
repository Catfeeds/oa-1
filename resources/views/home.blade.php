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
</style>
@endpush

@section('content')
    <meta name="referrer" content="no-referrer"/>
    <div class="wrapper wrapper-content">
        <div class="row" id="common">
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>常用功能</h2>
                    </div>
                    <div class="ibox-content no_padding">
                        <table class="table table-bordered">
                            <tr>
                                <td>
                                    <a href="{{ route('leave.create', ['id' => \App\Models\Sys\HolidayConfig::LEAVEID]) }}">
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-calendar-plus-o fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">请假</span>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('leave.create', ['id' => \App\Models\Sys\HolidayConfig::CHANGE]) }}">
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-star fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">加班调休</span>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('daily-detail.info') }}">
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-sticky-note fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">出勤记录</span>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a>
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-spinner fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">敬请期待</span>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <a>
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-spinner fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">敬请期待</span>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <a>
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-spinner fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">敬请期待</span>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a>
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-spinner fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">敬请期待</span>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <a>
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-spinner fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">敬请期待</span>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <a>
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-spinner fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">敬请期待</span>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a>
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-spinner fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">敬请期待</span>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <a>
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-spinner fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">敬请期待</span>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <a>
                                        <div class="text-center">
                                            <div>
                                                <span class="fa fa-spinner fa-3x"></span>
                                            </div>
                                            <span class="m-t-xs font-bold">敬请期待</span>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="ibox">
                    <div class="ibox-title text-center">
                        <h2>公告</h2>
                    </div>
                    <div class="ibox-content no_padding">
                        <div id="content">
                            <div id="bulletContent" class="pre-scrollable" style="padding: 0 15px">
                                {!! $bulletContent->content ?? '' !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-title text-center">
                        <h2>工作日历</h2>
                    </div>
                    <div class="ibox-content no_padding">
                        @include('widget.calendar-user', ['home' => 1])
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="work_station">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>工作台</h2>
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
                                                {{--<address>
                                                    <strong></strong><br>
                                                    <br>
                                                    <br>
                                                    <abbr></abbr>
                                                </address>--}}
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

        $('.table:first, #content').css('height', window.screen.height * 0.5);
    });
</script>
@endpush
