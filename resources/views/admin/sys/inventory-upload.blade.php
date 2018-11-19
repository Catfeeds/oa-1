@extends('admin.sys.sys')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="alert alert-info alert-warning">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    批量添加物品使用说明：<br>
                    1.使用Excel表格文件导入（.xlsx）。<br>
                    2.支持修改物品名称（导入的表格中将物品ID对应的物品名称修改）及添加新物品（导入的表格中填入正确的物品ID及物品名称）。<br>
                    3.只需要导入改动或新增的项目即可，后台原有物品信息不会被清空。<br>
                </div>
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}
                <div class="form-group">
                    {!! Form::label('img', trans('app.批量添加模板样式'), ['class' => 'control-label col-sm-3']) !!}
                    <div class="col-sm-6">
                        <img src="{{ asset('img/upload_inventory_example.png') }}" width="500px">
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('upload', trans('app.批量添加'), ['class' => 'control-label col-sm-3']) !!}
                    <div class="col-sm-5">
                        {!! Form::file('upload', ['class' => 'filestyle', 'data-icon' => false]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2 col-sm-offset-3">
                        {!! Form::submit('点击生成', ['class' => 'btn btn-primary']) !!}
                        <a href="javascript:history.go(-1)" class="btn btn-danger">返回</a>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection