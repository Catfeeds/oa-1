@extends('layouts.top-nav')

@section('title', $title)
@section('body-class', 'top-navigation')

@section('top-nav')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $title }}</h5>

                            @if(Entrust::can(['role-all', 'role']))
                            <div class="ibox-tools">
                                <a class="btn btn-xs btn-primary" href="{{ route('role') }}">
                                    {{ trans('app.列表', ['value' => trans('app.角色')]) }}
                                </a>
                            </div>
                            @endif

                        </div>
                        <div class="ibox-content">
                            <div class="well">
                                <h2>{{ $role->name }} | {{ $role->display_name }}</h2>
                                <small>{{ $role->description }}</small>
                            </div>
                            <div class="row">

                                {!! Form::open(['class' => 'form-horizontal']) !!}

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <button class="btn btn-primary btn-sm" type="submit">提交</button>
                                            <button class="btn btn-white btn-sm" type="reset">取消</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <i class="fa fa-info-circle"></i> 权限列表

                                            <div class="ibox-tools">
                                                <a class="btn btn-xs btn-primary disable-check-all"> 全选 </a>
                                                <a class="btn btn-xs btn-primary disable-un-check-all"> 全不选 </a>
                                            </div>

                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <div class="col-sm-12 disabled-item">
                                                    @foreach($permissionsGroup as $group => $permission)
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                {{ $group }}
                                                            </div>
                                                            <div class="panel-body">
                                                                @foreach($permission as $p)
                                                                <div class="checkbox col-sm-3">
                                                                    <label>
                                                                        <input type="checkbox" name="ps[]"
                                                                               @if(in_array($p['id'], array_keys($enables)))
                                                                                       checked
                                                                                       @endif
                                                                               value="{{ $p['id'] }}"> {{ $p['display_name'] }}
                                                                    </label>
                                                                </div>
                                                                @endforeach
                                                            </div>

                                                        </div>
                                                    @endforeach

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--<div class="col-lg-6">
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <i class="fa fa-info-circle"></i> 已有权限

                                            <div class="ibox-tools">
                                                <a class="btn btn-xs btn-primary enable-check-all"> 全选 </a>
                                                <a class="btn btn-xs btn-primary enable-un-check-all"> 全不选 </a>
                                            </div>

                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <div class="col-sm-10 enable-item">

                                                    @foreach($enables as $p)

                                                        <div class="checkbox"><label> <input type="checkbox"
                                                                                             name="ps[]"
                                                                                             value="{{ $p->id }}"
                                                                                             checked> {{ $p->display_name }}
                                                            </label></div>

                                                    @endforeach

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>--}}

                                {!! Form::close() !!}

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts-last')
    <script>
        $(function() {
            $('.disable-check-all').click(function() {
                $('.disabled-item').find("input[type=checkbox]").each(function() {
                    $(this).prop("checked", 'checked');
                })
            });
            $('.disable-un-check-all').click(function() {
                $('.disabled-item').find("input[type=checkbox]").each(function() {
                    $(this).prop("checked", false);
                })
            });
            $('.enable-check-all').click(function() {
                $('.enable-item').find("input[type=checkbox]").each(function() {
                    $(this).prop("checked", 'checked');
                })
            });
            $('.enable-un-check-all').click(function() {
                $('.enable-item').find("input[type=checkbox]").each(function() {
                    $(this).prop("checked", false);
                })
            });
        });
    </script>
@endsection