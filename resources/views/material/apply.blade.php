@extends('admin.sys.sys')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            @include('flash::message')
            <div class="ibox">
                <div class="ibox-title">
                    <h5>近期申请记录</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>名称</th>
                            <th>申请事由</th>
                            <th>预计归还时间</th>
                            <th>申请时间</th>
                            <th>状态</th>
                            <th>操作</th>
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
                                    <a href="{{ route('material.apply.info') }}">查看详情</a>
                                    <a href="{{ route('material.apply.redraw', ['id' => $apply['id']]) }}">撤回</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>资质库存列表</h5>
                </div>
                <div class="ibox-content">
                    <div class="m-b-md">
                        @include('widget.review-batch-operation-btn', ['btn' => [['submit-apply', '提交借用申请', 'btn-success']]])
                    </div>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th width="4%"></th>
                            <th>id</th>
                            <th>类型</th>
                            <th>名称</th>
                            <th>内容</th>
                            <th>说明</th>
                            <th>所属公司</th>
                            <th>库存数</th>
                            <th>预计归还时间</th>
                            <th>状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($inventory as $v)
                            <tr>
                                <td>
                                    @if(!empty($v->inv_remain))
                                        <input id="text_box" type="checkbox" class="i-checks" name="inventoryIds[]"
                                               value="{{ $v->id }}">
                                    @endif
                                </td>
                                <td>{{ $v->id }}</td>
                                <td>{{ $v->type }}</td>
                                <td>{{ $v->name }}</td>
                                <td>{{ $v->content }}</td>
                                <td>{{ $v->description }}</td>
                                <td>{{ $v->company }}</td>
                                <td>{{ $v->inv_remain }}</td>
                                @if(empty($v->inv_remain))
                                    <td>2018-10-10</td>
                                    <td>不可借用</td>
                                @else
                                    <td>--</td>
                                    <td>可借用</td>
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
@include('widget.icheck')

@push('scripts')
<script>
    $(function () {
        $('#submit-apply').click(function () {
            var invIds = [];
            $('.i-checks:checked').each(function (index, ele) {
                invIds.push($(ele).val());
            });
            $(location).prop('href', '{{ route('material.apply.create') }}' + '?inventoryIds=' + JSON.stringify(invIds));
        })
    });

</script>
@endpush