@extends('admin.sys.sys')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title ?? trans('app.系统设置') }}</h5>
                </div>
                <div class="ibox-content">

                    @include('flash::message')

                    <div class="panel-heading">
                        <div class="panel blank-panel">
                            <div class="panel-options">
                                <ul class="nav nav-tabs">
                                    @include('admin.sys._link-staff-tabs')
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane active">
                                <div class="ibox-content profile-content">
                                    {!! Form::open(['class' => 'form-horizontal']) !!}

                                    <div class="form-group @if (!empty($errors->first('ethnic'))) has-error @endif">
                                        {!! Form::label('ethnic', trans('staff.民族名称'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-3">
                                            {!! Form::text('ethnic', isset($ethnic->ethnic) ? $ethnic->ethnic: old('ethnic'), [
                                            'class' => 'form-control',
                                            'placeholder' => trans('app.请输入', ['value' => trans('staff.民族名称')]),
                                            'required' => true,
                                            ]) !!}
                                            <span class="help-block m-b-none">{{ $errors->first('ethnic') }}</span>
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>

                                    <div class="form-group @if (!empty($errors->first('sort'))) has-error @endif">
                                        {!! Form::label('sort', trans('staff.排序'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-3">
                                            {!! Form::number('sort', isset($ethnic->sort) ? $ethnic->sort: old('sort'), [
                                            'class' => 'form-control',
                                            'placeholder' => trans('app.请输入', ['value' => trans('staff.排序')]),
                                            'required' => true,
                                            'step' => 1
                                            ]) !!}
                                            <span class="help-block m-b-none">{{ $errors->first('sort') }}</span>
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>

                                    <div class="form-group">
                                        <div class="col-sm-3 col-sm-offset-4">
                                            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection