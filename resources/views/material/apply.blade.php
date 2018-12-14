@extends('sys.sys')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            @include('flash::message')
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ trans('material.近期申请记录') }}</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('material.apply.index-all') }}" style="color: #0a568c">{{ trans('material.显示更多') }}>></a>
                    </div>
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
        <div class="row">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ trans('material.资质库存列表') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="m-b-md">
                        @include('widget.review-batch-operation-btn', ['btn' =>
                            Entrust::can('material.apply.create') ? [['submit-apply', trans('material.提交借用申请'), 'btn-success']] : []
                        ])
                    </div>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th width="4%"></th>
                            <th>{{ trans('material.id') }}</th>
                            <th>{{ trans('material.类型') }}</th>
                            <th>{{ trans('material.名称') }}</th>
                            <th>{{ trans('material.内容') }}</th>
                            <th>{{ trans('material.说明') }}</th>
                            <th>{{ trans('material.所属公司') }}</th>
                            <th>{{ trans('material.库存数') }}</th>
                            <th>{{ trans('material.预计归还时间') }}</th>
                            <th>{{ trans('material.状态') }}</th>
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
                                    <td>{{ $expectReturn[$v->id] ?? NULL }}</td>
                                    <td>{{ trans('material.不可借用') }}</td>
                                @else
                                    <td>--</td>
                                    <td>{{ trans('material.可借用') }}</td>
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
            if (invIds.length == 0) {
                alert({{ trans('material.请选择以下库存再进行提交') }});
            }else {
                $(location).prop('href', '{{ route('material.apply.create') }}' + '?inventoryIds=' + JSON.stringify(invIds));
            }
        })
    });

</script>
@endpush