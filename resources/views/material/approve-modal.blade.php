<!-- 模态框 -->
<div class="modal fade" id="approve-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- 模态框头部 -->
            <div class="modal-header">
                <h4 class="modal-title">归还明细确认</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- 模态框主体 -->
            <div class="modal-body">
                @include('widget.review-batch-operation-btn', ['btn' => []])
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="4%"></th>
                        <th>id</th>
                        <th>类型</th>
                        <th>具体文件名称</th>
                        <th>数量</th>
                        <th>所属公司</th>
                        <th>内容</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($apply['inventory'] as $inv)
                        <tr>
                            <td>
                                @if(!empty($inv['inv_remain']))
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
                <button type="button" class="btn btn-success" style="margin-bottom: 0" name="submit">确认</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
            </div>

        </div>
    </div>
</div>
@include('widget.icheck')
