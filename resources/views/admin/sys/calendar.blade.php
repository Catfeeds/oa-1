@extends('admin.sys.sys')

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
<<<<<<< HEAD
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
=======
                    </div>

                    <div class="panel-body">
                        {{--收索区域--}}
                        <div>
                            {!! Form::open([ 'class' => 'form-inline', 'method' => 'post']) !!}
                            <div class="col-sm-2">
                                <select class="js-select2-single form-control" name="punch_rules_id" >
                                    <option value="">请选择排班规则</option>
                                    @foreach(\App\Models\Sys\PunchRules::getPunchRulesList() as $k => $v)
                                        <option value="{{ $k }}" >{{ $v }}</option>
                                    @endforeach
                                </select>
>>>>>>> 7a19d973410720acd4742ef69d0ea040d94c89aa
                            </div>
                            {!! Form::submit(trans('app.一键生成当月日历'), ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        </div>

<<<<<<< HEAD
                            <div class="panel-body">
                                {{--收索区域--}}
                                @include('widget.scope-month', ['scope' => $scope])
                                <div class="container">
                                    {!! Form::open([ 'class' => 'form-horizontal', 'method' => 'post', 'route' => 'calendar.storeMonth']) !!}
                                    <div class="form-group">
                                        {!! Form::label('week_num', trans('att.请选择批量生成星期'), ['class' => 'col-sm-2 control-label']) !!}
                                        @foreach(\App\Models\Sys\Calendar::$week as $k => $value)
                                            <div class="checkbox-inline col-xs-1">
                                                <label>
                                                    <input name="week_num" type="checkbox" class="i-checks" value="{{ $k }}" id="{{ "week_$k" }}">{{ $value }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('punch_rules_id', trans('att.请选择排班规则'), ['class' => 'col-sm-2 control-label']) !!}
                                        <div class="col-sm-2">
                                            <select class="js-select2-single form-control" name="punch_rules_id" >
                                                @foreach(\App\Models\Sys\PunchRules::getPunchRulesList() as $k => $v)
                                                    <option value="{{ $k }}" >{{ $v }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            {!! Form::submit(trans('app.点击生成'), ['class' => 'btn btn-primary', 'style' => 'float: right']) !!}
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane active">
                                        <div class="ibox-content profile-content">
                                            <div class="table-responsive">
                                                @if(($sm = date('m', strtotime($scope->startDate))) < ($em = date('m', strtotime($scope->endDate))))
                                                    @for($i = $sm; $i <=  $em; $i ++)
                                                        <div class="col-xs-4" style="margin-bottom: 5%">
                                                        @include('widget.calendar', ['date' => '2018-'.$i])
                                                        </div>
                                                    @endfor
                                                @endif
                                                <table class="table table-hover table-striped tooltip-demo">
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
                                                                    BaseHtml::tooltip(trans('app.设置'), route('calendar.edit', ['id' => $v['id']]))
                                                                !!}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                {{ $data->links() }}
                                            </div>
                                        </div>
=======
                        <div class="tab-content">
                            <div class="tab-pane active">
                                <div class="ibox-content profile-content">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped tooltip-demo">
                                            <thead>
                                            <tr>
                                                <th>{{ trans('app.年') }}</th>
                                                <th>{{ trans('app.月') }}</th>
                                                <th>{{ trans('app.日') }}</th>
                                                <th>{{ trans('app.周') }}</th>
                                                <th>{{ trans('app.排班规则') }}</th>
                                                <th>{{ trans('app.备注') }}</th>
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
                                                            BaseHtml::tooltip(trans('app.设置'), route('calendar.edit', ['id' => $v['id']]))
                                                        !!}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        {{ $data->links() }}
>>>>>>> 7a19d973410720acd4742ef69d0ea040d94c89aa
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