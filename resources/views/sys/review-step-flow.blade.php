@extends('sys.sys')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title ?? trans('app.系统设置') }}</h5>
                    <div class="ibox-tools">
                        @if(Entrust::can(['approval-step.create']))
                            <a class="btn btn-xs btn-primary" href="{{ route('review-step-flow.create') }}">
                                {{ trans('app.添加', ['value' => trans('app.审核流程配置')]) }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">

                    @include('flash::message')

                    <div class="panel-heading">
                        <div class="panel blank-panel">
                            <div class="panel-options">
                                <ul class="nav nav-tabs">
                                    @include('sys._link-tabs')
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane active">
                                <div class="ibox-content profile-content">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped tooltip-demo">
                                            <thead>
                                            <tr>
                                                <th>{{ trans('app.步骤ID') }}</th>
                                                <th>{{ trans('app.项目类型') }}</th>
                                                <th>{{ trans('app.项目子类型') }}</th>
                                                <th>{{ trans('app.限制最小条件') }}</th>
                                                <th>{{ trans('app.限制最大条件') }}</th>
                                                <th>{{ trans('app.是否允许修改审批人') }}</th>
                                                <th>{{ trans('app.审核流程') }}</th>
                                                <th>{{ trans('app.提交时间') }}</th>
                                                <th>{{ trans('app.操作') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($data as $v)
                                                <tr>
                                                    <td>{{ $v['step_id'] }}</td>
                                                    <td>{{ \App\Models\Sys\HolidayConfig::$allApplyType[$v['apply_type_id']] ?? '数据异常' }}</td>
                                                    <td>{{ \App\Models\Sys\HolidayConfig::holidayList()[$v['child_id']] ?? '暂无' }}</td>
                                                    <td>{{ $v['min_num'] ?? '暂无' }}</td>
                                                    <td>{{ $v['max_num'] ?? '暂无'}}</td>
                                                    <td>{{ \App\Models\Sys\ReviewStepFlow::$modifyType[$v['is_modify']] ?? '数据异常' }}</td>

                                                    <td>{{ implode('>>', $step[$v['step_id']] ?? []) }}</td>
                                                    <td>{{$v['created_at'] }}</td>
                                                    <td>
                                                        @if(Entrust::can(['approval-step.edit']))
                                                            {!!
                                                                BaseHtml::tooltip(trans('app.设置'), route('review-step-flow.edit', ['id' => $v['step_id']]))
                                                            !!}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        {{ $data->links() }}
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