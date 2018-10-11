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

                                            <div class="form-group @if (!empty($errors->first('dept'))) has-error @endif">
                                                {!! Form::label('dept', trans('app.部门名称'), ['class' => 'col-sm-4 control-label']) !!}
                                                <div class="col-sm-3">
                                                    {!! Form::text('dept', isset($dept->dept) ? $dept->dept: old('dept'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.部门名称')]),
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('dept') }}</span>
                                                </div>
                                            </div>

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