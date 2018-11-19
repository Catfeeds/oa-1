<!-- 模态框 -->
<div class="modal fade" id="approve-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- 模态框头部 -->
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('material.归还明细确认') }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- 模态框主体 -->
            <div class="modal-body">
                @include('widget.review-batch-operation-btn', ['btn' => []])
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="4%"></th>
                        <th>{{ trans('material.id') }}</th>
                        <th>{{ trans('material.类型') }}</th>
                        <th>{{ trans('material.具体文件名称') }}</th>
                        <th>{{ trans('material.数量') }}</th>
                        <th>{{ trans('material.所属公司') }}</th>
                        <th>{{ trans('material.内容') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($apply['inventory'] as $inv)
                        <tr>
                            <td>
                                @if($inv['pivot']['part'] == 0)
                                    <input id="text_box" type="checkbox" class="i-checks" name="inventoryIds[]"
                                           value="{{ $inv['id'] }}">
                                @endif
                            </td>
                            <td>{{ $inv['id'] }}</td>
                            <td>{{ $inv['type'] }}</td>
                            <td>{{ $inv['name'] }}</td>
                            <td>1</td>
                            <td>{{ $inv['company'] }}</td>
                            <td>{{ $inv['content'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 模态框底部 -->
            <div class="modal-footer">
                <button type="button" class="btn btn-success" style="margin-bottom: 0" name="submit">{{ trans('material.确认') }}</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('material.关闭') }}</button>
            </div>

        </div>
    </div>
</div>
@include('widget.icheck')
