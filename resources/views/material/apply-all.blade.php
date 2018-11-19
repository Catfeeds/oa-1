@extends('admin.sys.sys')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            @include('flash::message')
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{ trans('material.id') }}</th>
                            <th>{{ trans('material.名称') }}</th>
                            <th>{{ trans('material.申请事由') }}</th>
                            <th>{{ trans('material.预计归还时间') }}</th>
                            <th>{{ trans('material.申请时间') }}</th>
                            <th>{{ trans('material.状态') }}</th>
                            @if(Entrust::can(['material.apply.info', 'material.apply.redraw']))
                                <th>{{ trans('material.操作') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($applies as $apply)
                            <tr>
                                <td>{{ $apply['id'] }}</td>
                                <td>{{ $apply['inventory_name'] }}</td>
                                <td>{{ $apply['reason'] }}</td>
                                <td>{{ $apply['expect_return_time'] }}</td>
                                <td>{{ $apply['created_at'] }}</td>
                                <td>{{ \App\Models\Material\Apply::$stateChar[$apply['state']] }}</td>
                                <td>
                                    @if(Entrust::can('material.apply.info'))
                                        <a href="{{ route('material.apply.info', ['id' => $apply['id'], 'type' => \App\Models\Attendance\Leave::LOGIN_INFO]) }}">{{ trans('material.查看详情') }}</a>
                                    @endif
                                    @if(Entrust::can('material.apply.redraw'))
                                        @if($apply['state'] == \App\Models\Material\Apply::APPLY_SUBMIT)
                                            <a href="{{ route('material.apply.redraw', ['id' => $apply['id']]) }}">{{ trans('material.撤回') }}</a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection