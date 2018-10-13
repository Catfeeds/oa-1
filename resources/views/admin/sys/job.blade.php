@extends('admin.sys.sys')

@section('content')
    <div class="row">
        {{--收索区域--}}
        <div class="row m-b-md">
            <div class="col-xs-10">
                <div class="col-md-12">
                    <div class="form-inline">
                        {!! Form::open([ 'class' => 'form-inline', 'method' => 'get' ]) !!}
                        <div class="form-group">
                            {!! Form::text('job', $form['job'], [ 'class' => 'form-control m-b-xs', 'placeholder' => trans('app.岗位类型') ]) !!}
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
                    <h5>{{ $title ?? trans('app.系统设置') }}</h5>
                    <div class="ibox-tools">
                        @if(Entrust::can(['job.create']))
                            <a class="btn btn-xs btn-primary" href="{{ route('job.create') }}">
                                {{ trans('app.添加', ['value' => trans('app.岗位类型')]) }}
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
                                                <th>{{ trans('app.岗位类型ID') }}</th>
                                                <th>{{ trans('app.岗位类型名称') }}</th>
                                                <th>{{ trans('app.提交时间') }}</th>
                                                <th>{{ trans('app.操作') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($data as $v)
                                                <tr>
                                                    <td>{{ $v['job_id'] }}</td>
                                                    <td>{{ $v['job'] }}</td>
                                                    <td>{{ $v['created_at'] }}</td>
                                                    <td>
                                                        @if(Entrust::can(['job.edit']))
                                                        {!!
                                                            BaseHtml::tooltip(trans('app.设置'), route('job.edit', ['id' => $v['job_id']]))
                                                        !!}
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