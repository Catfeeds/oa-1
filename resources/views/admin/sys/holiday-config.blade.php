@extends('admin.sys.sys')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title ?? trans('app.系统设置') }}</h5>
                    <div class="ibox-tools">
                        @if(Entrust::can(['holiday-config.create']))
                            <a class="btn btn-xs btn-primary" href="{{ route('holiday-config.create') }}">
                                {{ trans('app.添加', ['value' => trans('app.申请配置')]) }}
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
                                                <th>{{ trans('app.配置ID') }}</th>
                                                <th>{{ trans('app.排序值') }}</th>
                                                <th>{{ trans('app.配置类型') }}</th>
                                                <th>{{ trans('app.配置名称') }}</th>
                                                <th>{{ trans('app.配置天数') }}</th>
                                                <th>{{ trans('app.配置描述') }}</th>
                                                <th>{{ trans('app.提交时间') }}</th>
                                                <th>{{ trans('app.操作') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($data as $v)
                                                <tr>
                                                    <td>{{ $v['holiday_id'] }}</td>
                                                    <td>{{ $v['sort'] }}</td>
                                                    <td>{{ \App\Models\Sys\HolidayConfig::$applyType[$v['apply_type_id']] ?? '' }}</td>
                                                    <td>{{ $v['holiday'] }}</td>
                                                    <td>{{ $v['num'] }}</td>
                                                    <td><pre style="width: 15em; height: 10em" >{{ $v['memo'] }}</pre></td>
                                                    <td>{{ $v['created_at'] }}</td>
                                                    <td>
                                                        @if(Entrust::can(['holiday-config.edit']))
                                                            {!!
                                                                BaseHtml::tooltip(trans('app.设置'), route('holiday-config.edit', ['id' => $v['holiday_id']]))
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