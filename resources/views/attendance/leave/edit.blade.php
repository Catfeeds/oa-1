@extends('attendance.side-nav')

@section('title', $title)

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">

                    {!! Form::open(['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}

                    <div class="form-group">
                        {!! Form::label('opening_time', trans('att.请假时间'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('opening_time', !empty($articles[0]->opening_time) ? $articles[0]->opening_time : (old('opening_time') ?? '') , [
                                'class' => 'form-control date',
                                ]) !!}
                                <span class="help-block m-b-none">{{ $errors->first('opening_time') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('opening_time', trans('att.请假时间'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('opening_time', !empty($articles[0]->opening_time) ? $articles[0]->opening_time : (old('opening_time') ?? '') , [
                                'class' => 'form-control date',
                                ]) !!}
                                <span class="help-block m-b-none">{{ $errors->first('opening_time') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('associate_image', trans('att.附件图片'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <img height="100px" width="100px" src="{{ !empty($articles[0]->associate_image) ?  asset($articles[0]->associate_image) : asset('img/blank.png') }}"
                                 id="show_associate_image">
                            <input name="associate_image" type="file" accept="image/*" id="select-associate-file"/>
                        </div></div>

                    <div class="hr-line-dashed"></div>



                    <div class="form-group">
                        {!! Form::label('content', trans('att.请假理由'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::textarea('content', $articles[0]->content ?? old('content'), [
                                'id' => 'editor'
                            ]) !!}
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                            <a href="javascript:history.go(-1);"
                               class="btn btn-info">{{ trans('att.返回列表') }}</a>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>

@endsection
@include('widget.icheck')
@include('widget.select2')
@section('scripts-last')
    <script>
        $(function() {
            function readURL(input, $class_id) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $($class_id).attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#select-thumb-file").change(function(){
                readURL(this, '#show_thumb_image');
            });

            $("#select-associate-file").change(function(){
                readURL(this, '#show_associate_image');
            });

            $("#select-mobile-header-file").change(function(){
                readURL(this, '#show_mobile_header_image');
            });
        });
    </script>
@endsection