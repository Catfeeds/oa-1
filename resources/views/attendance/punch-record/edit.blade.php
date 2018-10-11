@extends('attendance.side-nav')

@section('title', $title)

@section('content')

    <div class="alert alert-info alert-warning">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        上传文件规则说明：<br>
        1.文件格式为.xls格式。<br>
        2.从打卡机导出来的数据格式有问题,先另存为保存文件，再上传文件。<br>
        3.若文件导入成功，生成不了，请联系技术。
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">

                    {!! Form::open(['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}


                    <div class="form-group @if (!empty($errors->first('annex'))) has-error @endif">
                        {!! Form::label('annex', trans('att.打卡记录文件'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <input name="annex" type="file" accept="*.xls" id="select-associate-file"/>
                            <span class="help-block m-b-none">{{ $errors->first('annex') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('memo'))) has-error @endif">
                        {!! Form::label('memo', trans('att.打卡记录备注'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('memo', isset($punchRecord->memo) ? $punchRecord->memo: old('memo'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('att.打卡记录备注')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('memo') }}</span>
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