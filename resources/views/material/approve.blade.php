@extends('attendance.side-nav')

@push('css')

@endpush

@section('title', $title)

@section('content')
    <div class="wrapper wrapper">
        <div class="row">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>审批列表</h5>
                </div>
                <div class="ibox-content">
                    <div class="btn-group btn-group-sm m-b-sm">
                        <a class="btn {{ $state == 'all' ? 'btn-primary' : 'btn-default' }}"
                           href="{{ route('material.approve.state', ['state' => 'all']) }}">全部状态</a>
                        <a class="btn {{ $state == '0' ? 'btn-primary' : 'btn-default' }}"
                           href="{{ route('material.approve.state', ['state' => 0]) }}">待审批</a>
                        <a class="btn {{ $state == '1' ? 'btn-primary' : 'btn-default' }}"
                           href="{{ route('material.approve.state', ['state' => 1]) }}">借用中</a>
                        <a class="btn {{ $state == '2' ? 'btn-primary' : 'btn-default' }}"
                           href="{{ route('material.approve.state', ['state' => 2]) }}">已归还</a>
                    </div>
                    <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>类型</th>
                                <th>名称</th>
                                <th>所属公司</th>
                                <th>借用事由</th>
                                <th>预计归还时间</th>
                                <th>申请时间</th>
                                <th>申请人</th>
                                <th>状态</th>
                                <th>操作</th>
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
                                <td>
                                    <a href="{{ route('material.approve.info', ['id' => $apply['id']]) }}">查看详情</a>
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