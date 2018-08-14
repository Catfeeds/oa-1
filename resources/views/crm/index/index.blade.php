@extends('crm.side-nav')

@section('title', $title)

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="jumbotron">
                <h1>CRM系统管理 </h1>
                <p>{{ trans('app.请从左边菜单选择功能开始操作。') }}</p>
            </div>
        </div>
    </div>
@endsection
