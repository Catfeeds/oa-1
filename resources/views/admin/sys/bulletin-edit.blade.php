@extends('admin.sys.sys')

@push('css')
<link rel="stylesheet" href="{{ asset('ueditor/dialogs/xiumi-ue-v5.css') }}">
<style type="text/css">
    .wrapper .row .col-xs-5, .col-xs-3 {
        padding-left: 0;
    }
    .form-horizontal .control-label {
        text-align: left;
        padding-right: 0;
    }
    .form-horizontal .form-control {
        margin-left: -10px;
    }
</style>
@endpush
@section('content')

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal']) !!}
                <div class="row">
                    <div class="col-xs-5">
                        <div class="form-group">
                            {!! Form::label('title', '标题:', ['class' => 'col-xs-3 control-label']) !!}
                            <div class="col-xs-5">
                                {!! Form::text('title', $data->title ?? old('title'), [
                                'placeholder' => '输入公告标题', 'required' => true, 'class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="form-group @if (!empty($errors->first('valid_time'))) has-error @endif">
                            {!! Form::label('valid_time', '有效时间:', ['class' => 'col-xs-3 control-label']) !!}
                            <div class="col-xs-5">
                                {!! Form::text('valid_time', $data->valid_time ?? old('valid_time'), [
                                'placeholder' => '输入持续显示天数', 'required' => true, 'class' => 'form-control'
                            ]) !!}
                            </div>
                            <span class="text-danger">{{ $errors->first('valid_time') }}</span>
                        </div>

                        <div class="form-group @if (!empty($errors->first('weight'))) has-error @endif">
                            {!! Form::label('weight', '权重:', ['class' => 'col-xs-3 control-label']) !!}
                            <div class="col-xs-5">
                                {!! Form::text('weight', $data->weight ?? old('weight'), [
                                'placeholder' => '权重越大越靠前', 'required' => true, 'class' => 'form-control']) !!}
                            </div>
                            <span class="text-danger">{{ $errors->first('weight') }}</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                            <span>可点击<img src="http://xiumi.us/connect/ue/xiumi-connect-icon.png">进行编辑.之后可点击顶部对勾，复制，
                            就会退回并复制内容到编辑器里面</span>
                        <span class="text-danger">{{ $errors->first('content') }}</span>
                    </div>
                    <div class="form-group">
                        @include('flash::message')
                        <script id="container" name="content" type="text/plain" style="height:360px;">
                            {!! $data->content ?? old('content') !!}
                        </script>
                    </div>
                    <div class="form-group">
                        {!! Form::submit('提交', ['class' => 'btn btn-success']) !!}
                    </div>
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