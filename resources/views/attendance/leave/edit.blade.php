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

                    <div class="form-group @if (!empty($errors->first('holiday_id'))) has-error @endif">
                        {!! Form::label('holiday_id', trans('att.请假类型'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <select onchange="func()" class="js-select2-single form-control" id="holiday_id" name="holiday_id" >
                                <option value="">请选择</option>
                                @foreach($holidayList as $k => $v)
                                    <option value="{{ $k }}" @if($k === (int)old('holiday_id')) selected="selected" @endif>{{ $v }}</option>
                                @endforeach
                            </select>
                            <span class="help-block m-b-none">{{ $errors->first('holiday_id') }}</span>
                        </div>
                        <div class="col-sm-2">
                            <span id="show_memo" style="display: none"  style="color: red" class="help-block m-b-none">
                                <p style="color: red" id="show_p"></p>
                                <pre style="width: 30em; height: 10em"  id="show_pre"></pre>
                            </span>
                        </div>
                    </div>


                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('start_time'))) has-error @endif">
                        {!! Form::label('start_time', trans('att.请假开始时间'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('start_time', !empty($leave->start_time) ? $leave->start_time : (old('start_time') ?? '') , [
                                'class' => 'form-control date',
                                'required' => true,
                                ]) !!}
                                <span class="help-block m-b-none">{{ $errors->first('start_time') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <select class="js-select2-single form-control" name="start_id" >
                                @foreach(\App\Models\Attendance\Leave::$startId as $k => $v)
                                    <option value="{{ $k }}" @if($k === $leave->start_id) selected="selected" @endif>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('end_time'))) has-error @endif">
                        {!! Form::label('end_time', trans('att.请假结束时间'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('end_time', !empty($leave->end_time) ? $leave->end_time : (old('end_time') ?? '') , [
                                'class' => 'form-control date',
                                'required' => true,
                                ]) !!}
                                <span class="help-block m-b-none">{{ $errors->first('end_time') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <select class="js-select2-single form-control" name="end_id" >
                                @foreach(\App\Models\Attendance\Leave::$endId as $k => $v)
                                    <option value="{{ $k }}" @if($k === $leave->start_id) selected="selected" @endif>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('annex'))) has-error @endif">
                        {!! Form::label('annex', trans('att.附件图片'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <img height="100px" width="100px" src="{{ !empty($leave->annex) ?  asset($leave->annex) : asset('img/blank.png') }}"
                                 id="show_associate_image">

                            <input name="annex" type="file" accept="image/*" id="select-associate-file"/>
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('annex') }}</span>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('reason'))) has-error @endif">
                        {!! Form::label('reason', trans('att.请假理由'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::textarea('reason', $leave->reason ?? old('reason'), [
                            'required' => true,
                            ]) !!}
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('reason') }}</span>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('copy_user'))) has-error @endif">
                        {!! Form::label('copy_user', trans('att.抄送'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            <select multiple="multiple" class="js-select2-multiple form-control"
                                    name="copy_user[]">
                                @foreach($allUsers as $key => $val)
                                    <option value="{{ $val['user_id'] }}">{{ $val['alias'].'('.$val['username'].')' }}</option>
                                @endforeach
                            </select>
                            <span class="help-block m-b-none">{{ $errors->first('copy_user') }}</span>
                        </div>
                    </div>


                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                            <a href="{{route('leave.info')}}"
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
@include('widget.datepicker')
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

        function func() {
            var val = $('#holiday_id').children('option:selected').val();
            if(val != "") {
                $('#show_pre').html('');
                $.get('{{ route('leave.showMemo')}}', {id: val}, function ($data) {
                    if ($data.status == 1) {

                        if($data.show_memo){
                            $('#show_pre').html($data.memo);
                            $('#show_memo').show();
                        }
                        if($data.show_day){
                            $('#show_p').show();
                            $('#show_p').html($data.msg);
                        } else {
                            $('#show_p').hide();
                        }

                    } else {
                        $('#show_memo').hide();
                        $('#show_p').hide();
                        $('#show_pre').html('');
                    }
                })
            } else {
                $('#show_pre').html('');
                $('#show_memo').hide();
            }

        }
    </script>
@endsection