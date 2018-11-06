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
                        {!! Form::label('holiday_id', trans('att.调休类型'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <select onchange="showMemo()" id ='holiday_id' class="js-select2-single form-control" name="holiday_id" >
                                <option value="">请选择</option>
                                @foreach($holidayList as $k => $v)
                                    <option value="{{ $k }}" @if($k === (int)(!empty($leave->holiday_id) ? $leave->holiday_id : old('holiday_id'))) selected="selected" @endif>{{ $v }}</option>
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
                        {!! Form::label('start_time', trans('att.调休开始时间'), ['class' => 'col-sm-2 control-label']) !!}
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
                            <select  class="js-select2-single form-control" name="start_id" id="start_id" >
                                <option value="">-请选择时间点-</option>
                            </select>
                        </div>
                    </div>

                    {{--只有上级才可以显示批量选择人员--}}
                    @if(Auth::user()->is_leader === 1)
                        <div class="hr-line-dashed"></div>

                        <div class="form-group @if (!empty($errors->first('dept_users'))) has-error @endif">
                            {!! Form::label('dept_users', trans('att.批量申请人员'), ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-5">
                                <select multiple="multiple" class="js-select2-multiple form-control"
                                        name="dept_users[]">
                                    @foreach($deptUsers as $key => $val)
                                        <option value="{{ $val['user_id'] }}"
                                                @if (in_array($val['user_id'], $deptUsersSelected ?: old('dept_users') ?? [])) selected @endif>{{ $val['alias'].'('.$val['username'].')' }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block m-b-none">{{ $errors->first('dept_users') }}</span>
                            </div>
                        </div>
                    @endif
                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('annex'))) has-error @endif">
                        {!! Form::label('annex', trans('att.附件图片'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <img height="100px" width="100px" src="{{ !empty($leave->annex) ?  asset($leave->annex) : asset('img/blank.png') }}"
                                 id="show_associate_image">
                            <input name="annex" type="file" accept="image/*" id="select-associate-file"/>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('reason'))) has-error @endif">
                        {!! Form::label('reason', trans('att.申请理由'), ['class' => 'col-sm-2 control-label']) !!}
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

            showMemo();
        });

        /**
         * 显示剩余假期和描述
         */
        function showMemo() {
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
                            $("#start_id").select2("val", "");
                            $("#start_id").empty();
                            $("#start_id").select2({
                                placeholder: "-请选择时间点-", //默认所有
                                allowClear: true, //清楚选择项
                                multiple: false,// 多选
                                data: $data.point_list //绑定数据
                            });


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