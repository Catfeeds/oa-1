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

                            {!! Form::open([ 'class' => 'form-horizontal', 'method' => 'post', 'route' => 'calendar.storeMonth']) !!}
                            {!! Form::hidden('select_date', '') !!}

                            <div class="form-group">
                                {!! Form::label('week_num', trans('att.请选择批量生成星期'), ['class' => 'col-sm-2']) !!}
                                @foreach(\App\Models\Sys\Calendar::$week as $k => $value)
                                    <div class="checkbox-inline col-xs-1">
                                        <label>
                                            <input name="week_num" type="checkbox" class="i-checks" value="{{ $k }}" id="{{ "week_$k" }}">{{ $value }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group">
                                {!! Form::label('punch_rules_id', trans('att.请选择排班规则'), ['class' => 'col-sm-2']) !!}
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
                                    {!! Form::submit(trans('app.点击生成'), ['class' => 'btn btn-primary', 'style' => 'float: right', 'id' => 'submit']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="col-sm-2" style="float: right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-success">日历</button>
                                <button type="button" class="btn" id="list_button">列表</button>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane active">
                                <div class="ibox-content profile-content">
                                    <div class="table-responsive">
                                        <div id="calendar_show">
                                            {{--@if(($year = date('Y', strtotime($scope->startDate))) == date('Y', strtotime($scope->endDate)))
                                                @for($i = $startMonth; $i <=  $endMonth; $i ++)
                                                    <div class="col-xs-6 col-xs-offset-3">
                                                        @include('widget.calendar', ['date' => "$year-$i"])
                                                    </div>
                                                @endfor
                                            @endif--}}
                                            <?php $sd = strtotime(date('Y-m-01', strtotime($scope->startDate)));
                                            $ed = strtotime(date('Y-m-01', strtotime($scope->endDate)));
                                            ?>
                                            @for($y = $sd; $y <= $ed; $y = strtotime('+1 month', $y))
                                                <div class="col-xs-6 col-xs-offset-3">
                                                    @include('widget.calendar', ['date' => date('Y-m', $y)])
                                                </div>
                                            @endfor
                                        </div>
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
    //点击星期几,就选中星期几的日历列
    $('[name=week_num]').on('ifChecked', function () {
        var val = $(this).val();
        @for($y = $sd; $y <= $ed; $y = strtotime('+1 month', $y))
            $('#' + '{{ "calendar_".date('Y-m', $y) }}').find('td.fc-day').each(function (index, ele) {
            var date = new Date($(ele).attr('data-date'));
            var week = date.getDay() == 0 ? 7 : date.getDay();
            if (week == val && date.getMonth() + 1 == '{{ date('m', $y) }}' && $.inArray($(ele).attr('data-date'), selectDate[val]) == -1) {
                $(ele).css('background-color', '#e4edf5');
                selectDate[val].push($(ele).attr('data-date'));
            }
        });
        @endfor
    });

    //取消点击
    $('[name=week_num]').on('ifUnchecked', function () {
        var val = $(this).val();
        for (var i = 0; i < selectDate[val].length; i ++) {
            $('.fc-day').each(function (index, ele) {
                if ($(ele).attr('data-date') == selectDate[val][i]) {
                    $(this).css('background-color', '');
                }
            });
        }
        selectDate[val] = [];
    });

    $('#list_button').click(function () {
        $(location).attr('href', '{{ route('calendar.list') }}' +
            '?scope[startDate]={{ $scope->startDate }}&scope[endDate]={{ $scope->endDate }}');
    });

    $('#submit').click(function () {
        $('[name=select_date]').val(JSON.stringify(selectDate));
    });

    $(function () {
        $('#list_show').hide();
    });

</script>
@endpush