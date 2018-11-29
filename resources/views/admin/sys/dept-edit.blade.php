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
                                        <div class="col-sm-2">
                                            <div class="col-sm-3 col-sm-offset-1">
                                                {!! Form::button(trans('att.添加子部门'), ['class' => 'btn btn-info', 'id' => 'add_child']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    @if(!empty($parent))
                                        @foreach($parent as $k => $v)
                                            <div class="form-group">
                                                {!! Form::label('dept', trans('app.子部门名称'), ['class' => 'col-sm-5 control-label']) !!}
                                                <div class="col-sm-3">
                                                    {!! Form::text('child['.$v->dept_id.']', $v->dept, [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.子部门名称')]),
                                                    'required' => true,
                                                    ]) !!}
                                                </div>
                                                @if( !empty($v->dept_id) && Entrust::can(['dept.del']))
                                                    {!! BaseHtml::tooltip(trans('app.删除'), route('dept.del', ['id' => $v->dept_id]), ' fa-times text-danger fa-lg confirmation', ['data-confirm' => trans('确认删除['.$v->dept.']信息?')]) !!}
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif

                                    <div id="add_copy" class="form-group"></div>

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
@include('widget.bootbox')
@section('scripts-last')
    <script>
        $(function() {
            var i = 1;
            $('#add_child').click(function () {
                var clone = $('.ibox-content').find("#add_copy").clone(true).attr({'id': 'create_div_' + i});

                var html = '<label class="col-sm-5 control-label">子部门名称</label>' +
                    '<div class="col-sm-3 ">' +
                    ' <input class="form-control" name="child[]" placeholder="请输入子部门名称" type="text" value="" >' +
                    '</div>';

                clone.appendTo('#add_copy').html(html);
                i++;
            })

        });

    </script>
@endsection