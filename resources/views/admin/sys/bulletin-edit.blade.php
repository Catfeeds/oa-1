@extends('admin.sys.sys')

@push('css')
<link rel="stylesheet" href="{{ asset('ueditor/dialogs/xiumi-ue-v5.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/nouslider/nouislider.css') }}">
<style type="text/css">
    .prl-0{
        padding-left: 0;
        padding-right: 0;
    }
    .form-horizontal .control-label {
        text-align: left;
        padding-right: 0;
    }
    .noUi-value {
        margin-top: 5px;
    }
    .input-group .form-control {
        z-index: auto;
    }
    .set-width-250 {
        width: 350px;
    }
</style>
@endpush
@section('content')

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal']) !!}
                <div class="row">
                    <div class="col-xs-5 prl-0">
                        <div class="form-group">
                            {!! Form::label('title', trans('app.标题').':', ['class' => 'col-xs-3 control-label prl-0']) !!}
                            <div class="col-xs-8 prl-0" style="width: 175px">
                                {!! Form::text('title', $data->title ?? old('title'), [
                                'placeholder' => trans('app.输入公告标题'), 'required' => true, 'class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="form-group @if (!empty($errors->first('valid_time'))) has-error @endif">
                            {!! Form::label('valid_time', trans('app.有效时间').':', ['class' => 'col-xs-3 control-label prl-0']) !!}
                            @include('widget.daterangepicker')
                            <div class="col-xs-5 prl-0">
                                <div class="input-group date set-width-250">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="date-range-timestamp" class="form-control">
                                    {!! Form::hidden('start_date', $data->start_date ?? old('start_date') ?? date('Y-m-d'), ['id' => 'scope-start-date']) !!}
                                    {!! Form::hidden('end_date', $data->end_date ?? old('end_date') ?? date('Y-m-d'), ['id' => 'scope-end-date']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 40px">
                            {!! Form::label('weight', trans('app.权重').':', ['class' => 'col-xs-3 control-label prl-0']) !!}
                            <dic class="col-xs-5 prl-0">
                                <div id="slider" class="set-width-250"></div>
                                {!! Form::hidden('weight', $data->weight ?? old('weight') ?? '1.00') !!}
                            </dic>
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
                        {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-success mt-3']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" charset="utf-8" src="{{ asset('js/plugins/nouslider/nouislider.js') }}"></script>
    <!-- 配置文件 -->
    <script type="text/javascript" src="{{ asset('ueditor/ueditor.config.js') }}"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="{{ asset('ueditor/ueditor.all.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('ueditor/dialogs/xiumi-ue-dialog-v5.js') }}"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('container');
        var softSlider = document.getElementById('slider');

        noUiSlider.create(softSlider, {
            start: '{{ $data->weight ?? old('weight') ?? 1.00 }}',
            range: {
                'min': 0,
                '10%': 1,'20%': 2,'30%': 3,'40%': 4,'50%': 5,'60%': 6,'70%': 7,'80%': 8,'90%': 9,
                'max': 10
            },
            snap: true,
            tooltips: true,
            pips: {
                mode: 'values',
                values: [0, 5, 10],
                density: 10
            }
        });

        var tooltip = $(softSlider).find('.noUi-tooltip');
        var event = 1;
        tooltip.hide();

        //滑动时取消mouseOver的事件
        $('.noUi-handle').mouseover(function () {
            event === 1 ? tooltip.show() : '';
        }).mouseout(function () {
            event === 1 ? tooltip.hide() : '';
        });

        softSlider.noUiSlider.on('slide', function (values, handle) {
            event = 0;
            tooltip.show();
            $('[name=weight]').val(values[0]);
        });
        softSlider.noUiSlider.on('end', function () {
            event = 1;
            tooltip.hide();
        });
    </script>
@endpush