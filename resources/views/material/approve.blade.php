@extends('attendance.side-nav')

@push('css')

@endpush

@section('title', $title)

@section('content')
    <div class="wrapper wrapper">
        <div class="row">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="btn-group btn-group-sm m-b-sm">
                        <a class="btn {{ $state == 'all' ? 'btn-primary' : 'btn-default' }}"
                           href="{{ route('material.approve.index', ['state' => 'all']) }}">{{ trans('material.全部状态') }}</a>
                        <a class="btn {{ $state == '0' ? 'btn-primary' : 'btn-default' }}"
                           href="{{ route('material.approve.index', ['state' => 0]) }}">{{ trans('material.待审批') }}</a>
                        <a class="btn {{ $state == '1' ? 'btn-primary' : 'btn-default' }}"
                           href="{{ route('material.approve.index', ['state' => 1]) }}">{{ trans('material.借用中') }}</a>
                        <a class="btn {{ $state == '2' ? 'btn-primary' : 'btn-default' }}"
                           href="{{ route('material.approve.index', ['state' => 2]) }}">{{ trans('material.已归还') }}</a>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{ trans('material.id') }}</th>
                            <th>{{ trans('material.类型') }}</th>
                            <th>{{ trans('material.名称') }}</th>
                            <th>{{ trans('material.所属公司') }}</th>
                            <th>{{ trans('material.借用事由') }}</th>
                            <th>{{ trans('material.预计归还时间') }}</th>
                            <th>{{ trans('material.申请时间') }}</th>
                            <th>{{ trans('att.申请人') }}</th>
                            <th>{{ trans('material.状态') }}</th>
                            @if(Entrust::can('material.approve.info'))
                                <th>{{ trans('material.操作') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($applies as $apply)
                            <tr>
                                <td>{{ $apply['id'] }}</td>
                                <td>{{ $apply['inventory_type'] }}</td>
                                <td>{{ $apply['inventory_name'] }}</td>
                                <td>{{ $apply['inventory_company'] }}</td>
                                <td>{{ $apply['reason'] }}</td>
                                <td>{{ $apply['expect_return_time'] }}</td>
                                <td>{{ $apply['created_at'] }}</td>
                                <td>{{ \App\User::getUserAliasToId($apply['user_id'])->alias }}</td>
                                <td>{{ \App\Models\Material\Apply::$stateChar[$apply['state']] }}</td>
                                @if(Entrust::can('material.approve.info'))
                                    <td>
                                        <a href="{{ route('material.approve.info', ['id' => $apply['id'], 'type' => \App\Models\Attendance\Leave::LOGIN_VERIFY_INFO]) }}">{{ trans('material.查看详情') }}</a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('.btn-group button').click(function () {
            $('.btn-group button').removeClass('btn-primary btn-default').not(this).addClass('btn-default');
            $(this).addClass('btn-primary');
        })
    });
</script>
@endpush