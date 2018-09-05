@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent
    <div class="col-sm-8">
        <div class="title-action">
            @if(Entrust::can(['attendance-all', 'daily-detail.all', 'daily-detail.review']))
                <a href="{{ route('daily-detail.review.import.info') }}" class="btn btn-success btn-sm">{{ trans('导入打卡记录列表') }}</a>
                <a href="{{ route('daily-detail.review.import') }}" class="btn btn-primary btn-sm">{{ trans('导入打卡记录') }}</a>
            @endif
        </div>
    </div>

@endsection

@section('content')

    @include('flash::message')

    <div class="row">
        @include('widget.scope-date', ['scope' => $scope])
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table tooltip-demo table-bordered">
                            <thead>
                            <tr>
                                <th colspan="4" style="text-align: center">{{ trans('att.基础信息') }}</th>
                                <th colspan="6" style="text-align: center">{{ trans('att.考勤天数') }}</th>
                                <th colspan="3" style="text-align: center">{{ trans('att.扣分统计') }}</th>
                                <th colspan="3" style="text-align: center">{{ trans('att.剩余假期') }}</th>
                                <th colspan="1" style="text-align: center">{{ trans('att.操作') }}</th>
                            </tr>
                            <tr>
                                <th>月份</th>
                                <th>工号</th>
                                <th>姓名</th>
                                <th>部门</th>
                                <th>应到天数</th>
                                <th>实到天数</th>
                                <th>加班调休</th>
                                <th>无薪假</th>
                                <th>带薪假</th>
                                <th>全勤</th>
                                <th>迟到总分钟</th>
                                <th>其他</th>
                                <th>合计扣分</th>
                                <th>剩余年假</th>
                                <th>剩余调休假</th>
                                <th>剩余探亲假</th>
                                <th>当月明细</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $v)
                                <tr>
                                    <td>{{ $v['date'] }}</td>
                                    <td>{{ $v['user_id'] }}</td>
                                    <td>{{ $v['user_alias'] }}</td>
                                    <td>{{ $v['user_dept'] }}</td>
                                    <td>{{ $v['should_come'] }}</td>
                                    <td>{{ $v['actually_come'] }}</td>
                                    <td>{{ $v['overtime'] }}</td>
                                    <td>{{ $v['no_salary_leave'] }}</td>
                                    <td>{{ $v['has_salary_leave'] }}</td>
                                    <td>{{ $v['is_full_work'] }}</td>
                                    <td>{{ $v['late_num'] }}</td>
                                    <td>{{ $v['other'] }}</td>
                                    <td>{{ $v['deduct_num'] }}</td>
                                    <td>{{ $v['remain_year_holiday'] }}</td>
                                    <td>{{ $v['remain_change'] }}</td>
                                    <td>{{ $v['remain_visit'] }}</td>
                                    <td><a href="{{ route('daily-detail.review.user', ['id' => $v['user_id']]) }}">{{ $v['detail'] }}</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
