@extends('admin.sys.sys')

@push('css')
<style type="text/css">
    #calendar_show > div{
        margin-bottom: 5%;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title ?? trans('app.系统设置') }}</h5>
                    <div class="ibox-tools">
                        <a class="btn btn-xs btn-primary" href="{{ route('calendar.create') }}">
                            {{ trans('app.添加', ['value' => trans('app.日历表配置')]) }}
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('flash::message')
                    <div class="panel-heading">
                        <div class="panel blank-panel">
                            <div class="panel-options">
                                <ul class="nav nav-tabs">
                                    @include('admin.sys._link-tabs')
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        {{--收索区域--}}
                        <div class="container col-sm-8 col-sm-offset-1" id="config_calendar">
                            {!! Form::label('scope-month', trans('att.请先选择月份'), ['class' => 'col-sm-2 text-left']) !!}
                            <div class="col-sm-10">
                                @include('widget.scope-month', ['scope' => $scope])
                            </div>
                        </div>
                        <div class="col-sm-2" style="float: right">
                            <div class="btn-group">
                                <button type="button" class="btn" id="calendar_button">日历</button>
                                <button type="button" class="btn btn-success">列表</button>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane active">
                                <div class="ibox-content profile-content">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped tooltip-demo" id="list_show">
                                            <thead>
                                            <tr>
                                                <th>{{ trans('app.年') }}</th>
                                                <th>{{ trans('app.月') }}</th>
                                                <th>{{ trans('app.日') }}</th>
                                                <th>{{ trans('app.周') }}</th>
                                                <th>{{ trans('app.排班规则') }}</th>
                                                <th>{{ trans('app.备注') }}</th>
                                                <th>{{ trans('app.创建时间') }}</th>
                                                <th>{{ trans('app.操作') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($data as $v)
                                                <tr>
                                                    <td>{{ $v['year'] }}</td>
                                                    <td>{{ $v['month'] }}</td>
                                                    <td>{{ $v['day'] }}</td>
                                                    <td>{{ \App\Models\Sys\Calendar::$week[$v['week']] ?? ''}}</td>
                                                    <td>{{ \App\Models\Sys\PunchRules::getPunchRulesList()[$v['punch_rules_id']] ?? '' }}</td>
                                                    <td>{{ $v['memo'] }}</td>
                                                    <td>{{ $v['created_at'] }}</td>
                                                    <td>
                                                        {!!
                                                            BaseHtml::tooltip(trans('app.设置'), route('calendar.edit',
                                                            ['id' => $v['id'], 'back' => urlencode(request()->fullUrl())]))
                                                        !!}
                                                    </td>
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
            </div>
        </div>
    </div>
@endsection
@include('widget.select2')
@include('widget.icheck')

@push('scripts')
<script type="text/javascript">
    $(function () {
        $('#calendar_button').click(function () {
            $(location).attr('href', '{{ route('calendar') }}'  +
                '?scope[startDate]={{ $scope->startDate }}&scope[endDate]={{ $scope->endDate }}');
        });
    });
</script>
@endpush