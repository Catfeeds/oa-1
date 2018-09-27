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

                    <div class="form-group @if (!empty($errors->first('firm'))) has-error @endif">
                        {!! Form::label('firm', trans('staff.公司名称'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('firm', isset($firm->firm) ? $firm->firm: old('firm'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.公司名称')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('firm') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('alias'))) has-error @endif">
                        {!! Form::label('alias', trans('staff.公司别名'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('alias', isset($firm->alias) ? $firm->alias: old('alias'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.公司别名')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('alias') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                            <a href="{{ route('firm.list') }}"
                               class="btn btn-info">{{ trans('att.返回列表') }}</a>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>

@endsection