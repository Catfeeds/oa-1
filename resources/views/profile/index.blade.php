@extends('profile.profile')

@section('content-profile')

    <p><i class="fa fa-user"></i> {{ trans('app.账号') }} ：<strong>{{ $user->username }}</strong></p>
    <p><i class="fa fa-tag"></i> {{ trans('app.名称') }} ：{{ $user->alias }}</p>
    <p>
        <i class="fa fa-envelope-o"></i> {{ trans('app.邮箱') . '： ' . $user->email }}
        <a href="{{ url('profile/mail') }}">
            <button type="button" class="btn btn-xs btn-primary"><i
                        class="fa fa-envelope"></i>{{ trans('app.发送', ['value' => trans('app.测试邮件')]) }}</button>
        </a>
    </p>
    <p><i class="fa fa-user-plus"></i> {{ '创建日期： ' . $user->created_at }}</p>
    <p><i class="fa fa-star"></i> {{ '更新日期： ' . $user->updated_at }}</p>

@endsection
