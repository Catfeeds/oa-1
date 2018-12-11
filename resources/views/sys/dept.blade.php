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
                            {!! Form::text('dept', $form['dept'], [ 'class' => 'form-control m-b-xs', 'placeholder' => trans('app.部门名称') ]) !!}
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
                        @if(Entrust::can(['dept.create']))
                            <a class="btn btn-xs btn-primary" href="{{ route('dept.create') }}">
                                {{ trans('app.添加', ['value' => trans('app.部门')]) }}
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
                                                <th>{{ trans('app.部门ID') }}</th>
                                                <th>{{ trans('app.部门名称') }}</th>
                                                <th>{{ trans('app.子部门列表') }}</th>
                                                <th>{{ trans('app.提交时间') }}</th>
                                                <th>{{ trans('app.操作') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($data as $v)
                                                <tr>
                                                    <td>{{ $v['dept_id'] }}</td>
                                                    <td>{{ $v['dept'] }}</td>
                                                    <td>
                                                        @if(in_array($v['dept_id'], $parent))
                                                         <button type="button" class="btn btn-primary btn-xs show-parent"
                                                             data-id="{{ $v['dept_id'] }}">{{ trans('app.显示子部门') }}</button>
                                                        @else
                                                            {{ trans('app.无') }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $v['created_at'] }}</td>
                                                    <td>
                                                        @if(Entrust::can(['dept.edit']))
                                                            {!!
                                                                BaseHtml::tooltip(trans('app.设置'), route('dept.edit', ['id' => $v['dept_id']]))
                                                            !!}
                                                        @endif
                                                        @if(Entrust::can(['dept.del']))
                                                            {!! BaseHtml::tooltip(trans('app.删除'), route('dept.del', ['id' => $v['dept_id']]), ' fa-times text-danger fa-lg confirmation', ['data-confirm' => trans('确认删除['.$v['dept'].']信息?')]) !!}
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
    {{--部门信息弹窗--}}
    <div class="modal inmodal" id="child-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                class="sr-only">Close</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dept-child">
                            <thead>
                            <tr>
                                <th>{{ trans('app.子部门名称') }}</th>
                                <th>{{ trans('app.提交时间') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{--JQ获取数据写入--}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">{{ trans('app.关闭') }}</button>
                </div>
            </div>
        </div>
    </div>
    {{--部门信息弹窗end--}}

@endsection
@include('widget.bootbox')
@push('scripts')
<script>

    $(function () {
        $('.show-parent').click(function () {
            var id = $(this).data('id');
            show(id);
        });
    });

    function show(id) {
        $.getJSON('{{ route('dept.getChild') }}', {
            'id': id
        }, function (data) {

            if(data.data == '') $(this).hide();

            $('.modal-title').text('部门: ' + data.title);
            var html = '';
            $.each(data.data, function (k, v) {
                html += '<tr>' +
                    '       <td>' + v.dept + '</td>' +
                            '<td>' + v.created_at + '</td>' +
                        '</tr>';
            });
            $('.dept-child > tbody').html(html);
            $('#child-modal').modal('show');
        });
    }

</script>
@endpush