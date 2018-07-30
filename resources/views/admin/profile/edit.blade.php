@extends('admin.profile.profile')

@section('content-profile')

    {!! Form::open(['class' => 'form-horizontal']) !!}

    <div class="form-group @if (!empty($errors->first('email'))) has-error @endif">
        {!! Form::label('email', trans('app.邮箱'), ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-6">
            {!! Form::email('email', isset($user->email) ? $user->email : old('email'), [
            'class' => 'form-control',
            'placeholder' => trans('app.请输入', ['value' => trans('app.邮箱')]),
            'required' => true,
            ]) !!}
            <span class="help-block m-b-none">{{ $errors->first('email') }}</span>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
        </div>
    </div>

    {!! Form::close() !!}

@endsection

