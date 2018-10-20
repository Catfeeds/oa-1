@extends('admin.sys.sys')

@push('css')
<link rel="stylesheet" href="{{ asset('ueditor/dialogs/xiumi-ue-v5.css') }}">
<style type="text/css">
    .wrapper .row .col-xs-5, .col-xs-3 {
        padding-left: 0;
    }
</style>
@endpush
@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal']) !!}
                <div class="form-group">
                    <div class="col-xs-5">
                        {!! Form::label('title', '标题:', ['class' => 'col-xs-3']) !!}
                        {!! Form::text('title') !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-5">
                        {!! Form::label('valid_time', '有效时间:', ['class' => 'col-xs-3']) !!}
                        {!! Form::text('valid_time') !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-5">
                        {!! Form::label('weight', '权重:', ['class' => 'col-xs-3']) !!}
                        {!! Form::text('weight') !!}
                    </div>
                </div>

                <div class="form-group">
                    <span>可点击<img src="http://xiumi.us/connect/ue/xiumi-connect-icon.png">进行编辑.之后可点击顶部对勾，复制，
                        就会退回并复制内容到编辑器里面</span>
                </div>
                <div class="form-group">
                    <script id="container" name="content" type="text/plain"></script>
                </div>
                <div class="form-group">
                    {!! Form::submit('提交', ['class' => 'btn btn-success']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- 配置文件 -->
    <script type="text/javascript" src="{{ asset('ueditor/ueditor.config.js') }}"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="{{ asset('ueditor/ueditor.all.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('ueditor/dialogs/xiumi-ue-dialog-v5.js') }}"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('container');
    </script>
@endpush