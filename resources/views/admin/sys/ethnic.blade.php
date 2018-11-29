@extends('admin.sys.sys')

@section('content')
    <div class="row">
        {{--收索区域--}}
        <div class="row m-b-md">
            <div class="col-xs-12">
                <div class="col-md-12">
                    <div class="form-inline">
                        {!! Form::open([ 'class' => 'form-inline', 'method' => 'get' ]) !!}
                        <div class="form-group">
                            {!! Form::text('ethnic', $form['ethnic'], [ 'class' => 'form-control', 'placeholder' => trans('staff.民族名称') ]) !!}
                        </div>
                        {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary btn-sm m-l-md']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title ?? trans('app.系统配置') }}</h5>
                    <div class="ibox-tools">
                        @if(Entrust::can(['ethnic.create']))
                            <a class="btn btn-xs btn-primary" href="{{ route('ethnic.create') }}">
                                {{ trans('app.添加', ['value' => trans('staff.民族')]) }}
                            </a>
                        @endif
                    </div>
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
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped tooltip-demo">
                                            <thead>
                                            <tr>
                                                <th>{{ trans('staff.民族ID') }}</th>
                                                <th>{{ trans('staff.民族名称') }}</th>
                                                <th>{{ trans('staff.排序') }}</th>
                                                <th>{{ trans('staff.创建时间') }}</th>
                                                <th>{{ trans('att.操作') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($data as $v)
                                                <tr>
                                                    <td>{{ $v['ethnic_id'] }}</td>
                                                    <td>{{ $v['ethnic'] }}</td>
                                                    <td>{{ $v['sort'] }}</td>
                                                    <td>{{ $v['created_at'] }}</td>
                                                    <td>
                                                        @if(Entrust::can(['ethnic.edit']))
                                                            {!! BaseHtml::tooltip(trans('app.设置'), route('ethnic.edit', ['id' => $v['ethnic_id']]), 'cog fa fa-search') !!}
                                                        @endif
                                                        @if(Entrust::can(['ethnic.del']))
                                                            {!! BaseHtml::tooltip(trans('app.删除'), route('ethnic.del', ['id' => $v['ethnic_id']]), ' fa-times text-danger fa-lg confirmation', ['data-confirm' => trans('确认删除['.$v['ethnic'].']信息?')]) !!}
                                                        @endif
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
@endsection
@include('widget.bootbox')