@extends('attendance.side-nav')

@section('title', $title)

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        @if($errors->first('material'))
                            <div class="alert alert-danger alert-dismissable m-l-md m-r-md">
                                {{ $errors->first('material') }}
                            </div>
                        @endif
                        {!! Form::open(['method' => 'post', 'class' => 'form-horizontal', 'enctype' => "multipart/form-data"]) !!}
                        <div class="form-group @if($errors->first('expect_return_time')) has-error @endif">
                            {!! Form::label('expect_return_time', trans('material.预计归还时间'), ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    {!! Form::input('text', 'expect_return_time', '', ['class' => 'form-control date_time', 'required' => true]) !!}
                                </div>
                            </div>
                            <span class="help-block m-b-none">{{ $errors->first('expect_return_time') }}</span>
                        </div>

                        <div class="form-group @if($errors->first('annex')) has-error @endif">
                            {!! Form::label('annex', trans('material.附件图片'), ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-2">
                                <img height="100px" width="100px"
                                     src="{{ !empty($leave->annex) ? asset($leave->annex) : asset('img/blank.png') }}"
                                     id="show_associate_image">
                                {!! Form::file('annex', ['accept' => 'image/*', 'id' => 'select-associate-file']) !!}
                            </div>
                            <span class="help-block m-b-none">{{ $errors->first('annex') }}</span>
                        </div>

                        <div class="form-group">
                            {!! Form::label('reason', trans('material.借用事由'), ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-2">
                                {!! Form::textarea('reason', '', ['required' => true]) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('project', trans('material.借用项目'), ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-3">
                                <table class="table table-bordered m-b-none">
                                    <thead>
                                    <tr>
                                        <td>{{ trans('material.id') }}</td>
                                        <td>{{ trans('material.类型') }}</td>
                                        <td>{{ trans('material.名称') }}</td>
                                        <td>{{ trans('material.数量') }}</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($inventory as $v)
                                        <tr>
                                            <td>{{ $v->id }}</td>
                                            <td>{{ $v->type }}</td>
                                            <td>{{ $v->name }}</td>
                                            <td>1</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 col-sm-offset-3">
                                {!! Form::hidden('inventory_ids', $ids) !!}
                                {!! Form::submit(trans('material.提交'), ['class' => 'btn btn-primary']) !!}
                                <a href="javascript:history.go(-1)" class="btn btn-danger">{{ trans('material.返回') }}</a>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('widget.datepicker')