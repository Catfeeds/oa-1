<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">申诉事由</h4>
            </div>
            {!! Form::open(['method' => 'post', 'route' => 'appeal.store']) !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('message-text', '请填写理由:', ['class' => "control-label"]) !!}
                    {!! Form::textarea('reason', '', ['class' => 'form-control', 'id' => 'message-text', 'required' => true]) !!}
                    {!! Form::hidden('appeal_data', '') !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                {!! Form::submit('发送', ['class' => "btn btn-primary"]) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    $('#exampleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $(this).find('[name=appeal_data]').val(button.data('whatever'));
    })
</script>
@endpush