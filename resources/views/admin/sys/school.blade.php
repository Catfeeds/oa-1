@extends('admin.sys.sys')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $title ?? trans('app.游戏设置') }}</h5>
                            <div class="ibox-tools">
                                <a class="btn btn-xs btn-primary" href="{{ route('school.create') }}">
                                    {{ trans('app.添加', ['value' => trans('app.岗位')]) }}
                                </a>
                            </div>
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
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped tooltip-demo">
                                                    <thead>
                                                    <tr>
                                                        <th>{{ trans('app.学校ID') }}</th>
                                                        <th>{{ trans('app.学校名称') }}</th>
                                                        <th>{{ trans('app.提交时间') }}</th>
                                                        <th>{{ trans('app.操作') }}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($data as $v)
                                                        <tr>
                                                            <td>{{ $v['school_id'] }}</td>
                                                            <td>{{ $v['school'] }}</td>
                                                            <td>{{ $v['created_at'] }}</td>
                                                            <td>
                                                                {!!
                                                                    BaseHtml::tooltip(trans('app.设置'), route('school.edit', ['id' => $v['school_id']]))
                                                                !!}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                {{ $data->links() }}
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
    </div>

@endsection