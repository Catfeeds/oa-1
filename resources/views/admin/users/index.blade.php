@extends('layouts.top-nav')

@section('title', $title)
@section('body-class', 'top-navigation')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $title }}</h5>

                            @if(Entrust::can(['user.edit']))
                                <div class="ibox-tools">
                                    <a class="btn btn-xs btn-primary" href="{{ route('user.create') }}">
                                        {{ trans('app.添加员工') }}
                                    </a>
                                </div>
                            @endif

                        </div>
                        <div class="ibox-content profile-content">
                            <div class="row">
                                <div class="col-md-16">
                                    {!! Form::open([ 'class' => 'form-inline', 'method' => 'get' ]) !!}
                                    <div class="form-group">
                                        {!! Form::text('username', $form['username'], [ 'class' => 'form-control m-b-xs', 'placeholder' => trans('app.账号') ]) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::text('alias', $form['alias'], [ 'class' => 'form-control m-b-xs', 'placeholder' => trans('app.名称') ]) !!}
                                    </div>
                                    <div class="form-group">
                                        @include('widget.select-single', ['name' => 'role_id', 'lists' => $roleIds, 'selected' => $form['role_id']])
                                    </div>
                                    {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary btn-sm m-l-md']) !!}
                                    {!! Form::close() !!}
                                </div>
                            </div>
                            <div class="table-responsive">

                                @include('flash::message')

                                <table class="table table-hover table-striped tooltip-demo">
                                    <thead>
                                    <tr>
                                        <th>{{ trans('app.账号') }}</th>
                                        <th>{{ trans('app.姓名') }}</th>
                                        <th>{{ trans('app.邮箱') }}</th>
                                        <th>{{ trans('app.状态') }}</th>
                                        <th>验证登录</th>
                                        <th>{{ trans('app.部门') }}</th>
                                        <th>{{ trans('app.权限列表') }}</th>
                                        <th>{{ trans('app.创建时间') }}</th>
                                        <th>{{ trans('app.更新时间') }}</th>
                                        <th>{{ trans('app.操作') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($data as $v)
                                        <tr>
                                            <td>{{ $v['username'] }}</td>
                                            <td>{{ $v['alias'] }}</td>
                                            <td>{{ $v['email'] }}</td>
                                            <td>{!! \App\User::getStatusText($v['status']) !!}</td>
                                            <td>{!! \App\User::getIsMobileTest($v) !!}</td>
                                            <td>{{ $dept[$v['dept_id']] ?? '' }}</td>
                                            <td>{!! \App\Models\Role::getRoleName($roles, $v['role_id']) !!}</td>
                                            <td>{{ $v['created_at'] }}</td>
                                            <td>{{ $v['updated_at'] }}</td>
                                            <td>
                                                @if(Auth::user()->user_id != $v['user_id'] && Entrust::can(['user.edit']))
                                                    {!! BaseHtml::tooltip(trans('app.设置'), route('user.edit', ['id' => $v['user_id']])) !!}
                                                @endif

                                                @if(\Illuminate\Support\Facades\Redis::exists(md5($v['user_id'] .'_' . $v['username'])))
                                                    {!! BaseHtml::tooltip(trans('app.发送密码到邮箱'), route('user.sendEmail', ['id' => $v['user_id']]), 'envelope') !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>

                                {{ $data->links() }}

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@include('widget.select2')
