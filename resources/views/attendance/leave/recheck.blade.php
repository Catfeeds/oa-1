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
                    {{ Form::hidden('leave_id', $leave->leave_id ?? '') }}
                    <div class="form-group @if (!empty($errors->first('holiday_id'))) has-error @endif">
                        {!! Form::label('holiday_id_label', trans('att.补打卡类型'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            @foreach($holidayList as $k => $v)
                                <label class="radio-inline i-checks">
                                    <input type="radio" id="holiday_id" name="holiday_id" value="{{$k}}" @if($k === (int)($leave->holiday_id ?? old('holiday_id'))) checked @endif> {{ $v }}
                                </label>
                            @endforeach
                        </div>
                        <div class="col-sm-2">
                            <span id="show_memo" style="display: none"  class="help-block m-b-none">
                                <p style="color: red" id="show_p"></p>
                                <pre style="width: 30em; height: 10em"  id="show_pre"></pre>
                            </span>
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('holiday_id') }}</span>
                    </div>

                    <div class="hr-line-dashed"></div>
                    <div class="form-group @if (!empty($errors->first('start_time'))) has-error @endif" >
                        {!! Form::label('start_time', trans('att.补打卡时间'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('start_time', $time, [
                                'class' => 'form-control date_time_hi',
                                'id' => 'start_time'
                                ]) !!}
                                <span class="help-block m-b-none">{{ $errors->first('start_time') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('annex'))) has-error @endif">
                        {!! Form::label('annex', trans('att.附件图片'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <div id="outerdiv" style="position:fixed;top:0;left:0;background:rgba(0,0,0,0.7);z-index:2;width:100%;height:100%;display:none;">
                                <div id="innerdiv" style="position:absolute;">
                                    <img id="bigimg" style="border:5px solid #fff;" src="" />
                                </div>
                            </div>
                            <img height="100px" width="100px" src="{{ !empty($leave->annex) ?  asset($leave->annex) : asset('img/blank.png') }}"
                                 id="show_associate_image">
                            <input name="annex" type="file" accept="image/*" id="select-associate-file"/>
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('annex') }}</span>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('reason'))) has-error @endif">
                        {!! Form::label('reason', trans('att.补打卡理由'), ['class' => 'col-sm-2 control-label']) !!}
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

                    <div class="form-group @if (!empty($errors->first('annex'))) has-error @endif">
                        {!! Form::label('annex', trans('att.审批流程'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-9">
                            <div id="show_step" class="form-inline">

                            </div>
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('annex') }}</span>
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
@include('widget.show-img')
@section('scripts-last')
    <script>
        $(function() {

            $('#show_associate_image').click(function () {
                showImg("#outerdiv", "#innerdiv", "#bigimg", $(this));
            });

            showMemo();

            $('input[type="radio"]').on('ifChecked', function () {
                showMemo();
            });

            @if(!empty($daily))
                @if(empty($daily->punch_start_time))
                    $('#recheck_1').iCheck('check');
                    @if(!empty($daily->punch_end_time))
                        $('#recheck_2').parents('.checkbox').remove();
                        $('#end_time').val("{{ $daily->day }}" + " 20:00:00");
                    @endif
                    $('#start_time').val("{{ $daily->day }}" + " 09:00:00");
                    $('#onwork_div').show();
                @endif

                @if(empty($daily->punch_end_time))
                    $('#recheck_2').iCheck('check');
                    @if(!empty($daily->punch_start_time))
                        $('#recheck_1').parents('.checkbox').remove();
                        $('#start_time').val("{{ $daily->day }}" + " 09:00:00");
                    @endif
                    $('#end_time').val("{{ $daily->day }}" + " 20:00:00");
                    $('#offwork_div').show();
                @endif
            @endif
        });

        /**
         * 显示剩余假期和描述
         */
        function showMemo() {
            var hid = $('input:radio:checked').val();
            if(hid != "" && val != null) {
                $('#show_pre').html('');
                $.get('{{ route('leave.showMemo')}}', {id: hid}, function ($data) {
                    if ($data.status == 1) {
                        /*显示描述*/
                        if($data.show_memo) {
                            $('#show_pre').html($data.memo);
                            $('#show_memo').show();
                        }
                        var html = $data.step;
                        $('#show_step').html(html).find('select').select2();
                        inquire(hid);

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

        function inquire(hid) {
            var holidayId = hid;
            var startTime = $('#start_time').val();

            if(holidayId != '') {
                $.get('{{ route('leave.inquire')}}', {holidayId: holidayId,startTime: startTime}, function ($data) {
                    if ($data.status == 1) {
                        $('#show_step').html($data.step).find('select').select2();
                    } else {
                        $('#show_step').html('');
                    }
                })
            }

        }


    </script>
@endsection