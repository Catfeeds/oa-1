@extends('admin.sys.sys')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        {!! Form::open(['method' => 'post', 'class' => 'form-horizontal']) !!}
                        <div class="form-group">
                            {!! Form::label('type', '类型', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-2">
                                {!! Form::input('text', 'type', $data->type ?? old('type') ?? '', ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('name', '具体文件名', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-2">
                                {!! Form::input('text', 'name', $data->name ?? old('name') ?? '', ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                        </div>
                        <div class="form-group @if (!empty($errors->first('inv_remain'))) has-error @endif">
                            {!! Form::label('inv_remain', '库存总数', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-2">
                                {!! Form::input('text', 'inv_remain', $data->inv_remain ?? old('inv_remain') ?? '', ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                            <span class="help-block m-b-none">{{ $errors->first('inv_remain') }}</span>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company', '所属公司', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-2">
                                {!! Form::input('text', 'company', $data->company ?? old('company') ?? '', ['class' => 'form-control', 'require' => true]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('content', '内容', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-2">
                                {!! Form::textarea('content', $data->content ?? old('content') ?? '',
                                ['class' => 'form-control', 'style' => 'height: 100px', 'require' => true]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('description', '说明', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-2">
                                {!! Form::textarea('description', $data->description ?? old('description') ?? '',
                                ['class' => 'form-control', 'style' => 'height: 100px', 'require' => true]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('is_annex', '是否要求上传附件', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-4">
                                @foreach(\App\Models\Sys\Inventory::$isShow as $k => $v)
                                    <label class="radio-inline i-checks">
                                        {!! Form::radio('is_annex', $k, $k === ($data->is_annex ??
                                         old('is_annex') ?? \App\Models\Sys\Inventory::STATUS_DISABLE), [
                                    ]) !!} {{ $v }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('is_show', '是否开启', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-4">
                                @foreach(\App\Models\Sys\Inventory::$isShow as $k => $v)
                                    <label class="radio-inline i-checks">
                                        {!! Form::radio('is_show', $k, $k === ($data->is_show ??
                                         old('is_show') ?? \App\Models\Sys\Inventory::STATUS_DISABLE)) !!}
                                        {{ $v }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-3 m-t-sm">
                                {!! Form::submit('提交', ['class' => 'btn btn-primary']) !!}
                                <a class="btn btn-danger" href="javascript:history.go(-1)">返回</a>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('widget.icheck')