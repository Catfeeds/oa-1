@extends('admin.sys.sys')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title ?? trans('app.系统设置') }}</h5>
                    <div class="ibox-tools">
                        @if(Entrust::can(['punch-rules.create']))
                            <a class="btn btn-xs btn-primary" href="{{ route('punch-rules.create') }}">
                                {{ trans('app.添加', ['value' => trans('app.上下班时间规则配置')]) }}
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
                                    @include('admin.sys._link-tabs')
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
                                                <th>{{ trans('app.规则ID') }}</th>
                                                <th>{{ trans('app.规则类型') }}</th>
                                                <th>{{ trans('app.规则名称') }}</th>
                                                <th>{{ trans('app.上班准备时间') }}</th>
                                                <th>{{ trans('app.上班时间') }}</th>
                                                <th>{{ trans('app.下班时间') }}</th>
                                                <th>{{ trans('app.提交时间') }}</th>
                                                <th>{{ trans('app.操作') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($data as $v)
                                                <tr>
                                                    <td>{{ $v['id'] }}</td>
                                                    <td>{{ \App\Models\Sys\PunchRules::$punchType[$v['punch_type_id']] ?? '' }}</td>
                                                    <td>{{ $v['name'] }}</td>
                                                    <td>{{ $v['ready_time'] }}</td>
                                                    <td>{{ $v['work_start_time'] }}</td>
                                                    <td>{{ $v['work_end_time'] }}</td>
                                                    <td>{{ $v['created_at'] }}</td>
                                                    <td>
                                                        @if(Entrust::can(['punch-rules.edit']))
                                                            {!!
                                                                BaseHtml::tooltip(trans('app.设置'), route('punch-rules.edit', ['id' => $v['id']]))
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