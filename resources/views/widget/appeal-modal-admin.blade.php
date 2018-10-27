<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">申诉事由</h4>
            </div>
            {!! Form::open(['method' => 'post', 'route' => 'appeal.update']) !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('message-text', '可填写备注:', ['class' => "control-label"]) !!}
                    {!! Form::textarea('remark', '', ['class' => 'form-control', 'id' => 'message-text']) !!}
                    {!! Form::hidden('operate_user_id', \Auth::user()->user_id) !!}
                    {!! Form::hidden('appeal_id', '') !!}
                    {!! Form::hidden('result', '') !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="accept"></button>
                <button type="button" class="btn btn-danger" id="deny"></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    $(function () {
        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            $(this).find('[name=appeal_id]').val(button.data('whatever'));
            $(this).find('[name=remark]').val(button.data('remark'));
            $(this).find('#accept').text(button.data('result') == 1 ? '已选择接受' : '接受');
            $(this).find('#deny').text(button.data('result') == 2 ? '已选择拒绝' : '拒绝');
        });

        $('#accept').click(function () {
            $('[name=result]').val(1);
            $('form').submit();
        });

        $('#deny').click(function () {
            $('[name=result]').val(2);
            $('form').submit();
        });
    });
</script>
@endpush