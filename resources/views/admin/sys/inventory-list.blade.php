@extends('admin.sys.sys')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                    <div class="ibox-tools">
                        @if(Entrust::can('material.inventory-upload'))
                            <a href="{{ route('inventory.upload') }}" class="btn btn-primary btn-xs">批量添加资质借用</a>
                        @endif
                        @if(Entrust::can('material.inventory-create'))
                            <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-xs">添加资质借用</a>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    @include('flash::message')
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>类型</th>
                            <th>名称</th>
                            <th>内容</th>
                            <th>说明</th>
                            <th>所属公司</th>
                            <th>库存总数</th>
                            @if(Entrust::can('material.inventory-edit'))
                                <th>操作</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $v)
                            <tr>
                                <td>{{ $v->id }}</td>
                                <td>{{ $v->type }}</td>
                                <td>{{ $v->name }}</td>
                                <td>{{ $v->content }}</td>
                                <td>{{ $v->description }}</td>
                                <td>{{ $v->company }}</td>
                                <td>{{ $v->inv_remain }}</td>
                                @if(Entrust::can('material.inventory-edit'))
                                    <td><a href="{{ route('inventory.edit', ['id' => $v->id]) }}">修改</a></td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection