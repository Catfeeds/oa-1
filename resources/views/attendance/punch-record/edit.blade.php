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

                    <div class="form-group @if (!empty($errors->first('name'))) has-error @endif">
                        {!! Form::label('name', trans('att.记录名称'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('name', isset($dept->dept) ? $dept->dept: old('name'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('att.记录名称')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('name') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('annex'))) has-error @endif">
                        {!! Form::label('annex', trans('att.打卡记录文件'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <input name="annex" type="file" accept="*.xls" id="select-associate-file"/>
                            <span class="help-block m-b-none">{{ $errors->first('annex') }}</span>
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