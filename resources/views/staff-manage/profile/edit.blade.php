@extends('staff-manage.profile.profile')

@section('content-profile')

    {!! Form::open(['class' => 'form-horizontal']) !!}

    <div class="form-group @if (!empty($errors->first('live_address'))) has-error @endif">
        {!! Form::label('live_address', trans('app.居住地址'), ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-6">
            {!! Form::text('live_address', isset($user->userExt->live_address) ? $user->userExt->live_address : old('live_address'), [
            'class' => 'form-control',
            'placeholder' => trans('app.请输入', ['value' => trans('app.居住地址')]),

            ]) !!}
            <span class="help-block m-b-none">{{ $errors->first('live_address') }}</span>
        </div>
    </div>

    <div class="hr-line-dashed"></div>

    <div class="form-group @if (!empty($errors->first('urgent_name'))) has-error @endif">
        {!! Form::label('urgent_name', trans('app.紧急联系人姓名'), ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-6">
            {!! Form::text('urgent_name', isset($user->userExt->urgent_name) ? $user->userExt->urgent_name : old('urgent_name'), [
            'class' => 'form-control',
            'placeholder' => trans('app.请输入', ['value' => trans('app.紧急联系人姓名')]),

            ]) !!}
            <span class="help-block m-b-none">{{ $errors->first('live_address') }}</span>
        </div>
    </div>

    <div class="hr-line-dashed"></div>

    <div class="form-group @if (!empty($errors->first('urgent_tel'))) has-error @endif">
        {!! Form::label('urgent_tel', trans('app.紧急联系人电话'), ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-6">
            {!! Form::text('urgent_tel', isset($user->userExt->urgent_tel) ? $user->userExt->urgent_tel : old('urgent_tel'), [
            'class' => 'form-control',
            'placeholder' => trans('app.请输入', ['value' => trans('app.紧急联系人电话')]),

            ]) !!}
            <span class="help-block m-b-none">{{ $errors->first('urgent_tel') }}</span>
        </div>
    </div>

    <div class="hr-line-dashed"></div>

    <div class="form-group">
        {!! Form::label('marital_status', trans('app.婚姻状况'), ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-2">
            <select class="js-select2-single form-control" name="marital_status" >
                @foreach(\App\Models\UserExt::$marital as $k => $v)
                    <option value="{{ $k }}" @if($k === $user->userExt->marital_status) selected="selected" @endif>{{ \App\Models\UserExt::$marital[$k] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
        </div>
    </div>

    {!! Form::close() !!}

@endsection
@include('widget.select2')

