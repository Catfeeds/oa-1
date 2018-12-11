@extends('sys.sys')

@section('content')
    <div class="row">
        {{--收索区域--}}
        <div class="row m-b-md">
            <div class="col-xs-10">
                <div class="col-md-12">
                    <div class="form-inline">
                        {!! Form::open([ 'class' => 'form-inline', 'method' => 'get' ]) !!}
                        <div class="form-group">
                            {!! Form::text('school', $form['school'], [ 'class' => 'form-control m-b-xs', 'placeholder' => trans('app.学校名称') ]) !!}
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
                    <h5>{{ $title ?? trans('app.游戏设置') }}</h5>
                    <div class="ibox-tools">
                        @if(Entrust::can(['school.create']))
                            <a class="btn btn-xs btn-primary" href="{{ route('school.create') }}">
                                {{ trans('app.添加', ['value' => trans('app.学校')]) }}
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
                                    @include('sys._link-staff-tabs')
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
                                                        @if(Entrust::can(['school.edit']))
                                                            {!!
                                                                BaseHtml::tooltip(trans('app.设置'), route('school.edit', ['id' => $v['school_id']]))
                                                            !!}
                                                        @endif

                                                        @if(Entrust::can(['school.del']))
                                                            {!! BaseHtml::tooltip(trans('app.删除'), route('school.del', ['id' => $v['school_id']]), ' fa-times text-danger fa-lg confirmation', ['data-confirm' => trans('确认删除['.$v['school'].']信息?')]) !!}
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
    </div>s
@endsection
@include('widget.bootbox')