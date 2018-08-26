@extends('admin.sys.sys')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $title ?? trans('app.游戏设置') }}</h5>
                        </div>
                        <div class="ibox-content">

                            @include('flash::message')

                            <div class="panel-heading">
                                <div class="panel blank-panel">
                                    <div class="panel-options">
                                        <ul class="nav nav-tabs">
                                            @include('admin.sys._link-tabs')
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="tab-content">
                                    <div class="tab-pane active">
                                        <div class="ibox-content profile-content">
                                            {!! Form::open(['class' => 'form-horizontal']) !!}

                                            <div class="form-group @if (!empty($errors->first('name'))) has-error @endif">
                                                {!! Form::label('name', trans('app.步骤名称'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    {!! Form::text('name', isset($step->name) ? $step->name: old('name'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.步骤名称')]),
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('name') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('min_day')) || !empty($errors->first('max_day'))) has-error @endif">
                                                {!! Form::label('valid_time', trans('app.假期时间范围'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        {!! Form::text('min_day', $step->min_day ?? old('min_day'), [
                                                        'class' => 'input-sm form-control',
                                                        'placeholder' => trans('app.请输入', ['value' => trans('app.最小天数')]),
                                                        ]) !!}
                                                        <span class="input-group-addon" style="background-color:#eeeeee;">to</span>
                                                        {!! Form::text('max_day', $step->max_day ?? old('max_day'), [
                                                        'class' => 'input-sm form-control',
                                                        'placeholder' => trans('app.请输入', ['value' => trans('app.最大天数')]),
                                                        ]) !!}
                                                    </div>
                                                    <span class="help-block m-b-none">{{ $errors->first('min_day') }}</span>
                                                    <span class="help-block m-b-none">{{ $errors->first('max_day') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('step'))) has-error @endif" id="checkbox_div">
                                                {!! Form::label('step', trans('app.职务'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                    <label class="checkbox-inline col-sm-12"> </label>
                                                    @foreach($roleList as $id=> $alias)
                                                        <label class="checkbox-inline col-sm-2">
                                                            <?php $isChecked = in_array($id, $roleId) ? true : false; ?>
                                                            {!! Form::checkbox('step[]', $id, $isChecked) . $alias !!}
                                                                <input type="hidden" id="check_step_{{$id}}" name="check_step[]" value="{{$checkStep[$id] ?? '' }}">
                                                                <button @if (!isset($stepId[$id])) style="display: none"  @endif  id="check_{{$id}}" class="btn btn-danger btn-circle btn-outline" type="button">{{$stepId[$id] ?? ''}}</button>
                                                        </label>
                                                    @endforeach
                                                    <span class="help-block m-b-none">{{ $errors->first('step') }}</span>
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
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-last')
    <script>
        $(function() {
            var i = {{$maxStep ?? 0}};

            $('#checkbox_div input:checkbox').change(function () {
                var id = $(this).val();
                if($(this).is(':checked')) {
                    i++;
                    $('#check_' + id).html(i);
                    $('#check_step_'+ id).val(i +'$$'+ id);
                    $('#check_' + id).show();
                } else {
                    var num  = i;
                    i--;
                    var check = $('#check_' + id).html();
                    $('#check_step_'+ id).val('');
                    /*重新赋值*/
                    $('input:checkbox').each(function(){
                        if($(this).is(':checked')) {
                            var cid = $(this).val();
                            var check_cid = $('#check_' + cid).html();

                            if(check_cid >= num) {
                                $('#check_' + cid).html(check_cid - 1);
                                $('#check_step_'+ cid).val(check_cid -1 +'$$'+ cid);
                            }else if(check < $('#check_' + cid).html() && check < num && $('#check_' + cid).html() >= 1) {
                                $('#check_step_'+ cid).val(check_cid -1 +'$$'+ cid);
                                $('#check_' + cid).html(check_cid - 1);
                            }
                        }
                    });

                    $('#check_' + id).hide();
                }
            });
        });
    </script>
@endsection