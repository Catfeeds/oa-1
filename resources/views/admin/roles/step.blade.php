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
                        </div>
                        <div class="ibox-content">

                            {!! Form::open(['class' => 'form-horizontal']) !!}

                            <div class="form-group">
                                {!! Form::label('name', trans('app.职务名称'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {{-- 职务名称不可编辑 --}}
                                    {!! Form::text('name', $role->name, [
                                    'class' => 'form-control',
                                    'disabled',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('step_id'))) has-error @endif">
                                {!! Form::label('step_id', trans('app.审批步骤模块'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    <label class="checkbox-inline col-sm-12"> <input type="checkbox" id="check-all">{{ trans('app.选择所有') }}</label>
                                    @foreach($steps as $s)
                                        <label class="checkbox-inline col-sm-2">
                                            {!! Form::checkbox('step_id[]', $s->step_id, in_array($s->step_id, $stepList)) . $s->name !!}
                                        </label>
                                    @endforeach
                                    <span class="help-block m-b-none col-sm-12">{{ $errors->first('step_id') }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
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

@endsection

@section('scripts-last')
    <script>
        $(function() {
            $('#check-all').click(function () {
                if($(this).prop('checked')) {
                    $("input[type=checkbox]").each(function () {
                        $(this).prop("checked", 'checked');
                    });
                } else {
                    $("input[type=checkbox]").each(function () {
                        $(this).prop("checked", false);
                    });
                }
            });
        });
    </script>
@endsection