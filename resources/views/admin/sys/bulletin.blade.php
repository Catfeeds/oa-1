@extends('admin.sys.sys')

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="ibox">
            <div class="ibox-title">
                <h5>公告发布显示列表</h5>
                <div class="ibox-tools">
                    {{--@if(Entrust::can(['bulletin.create']))--}}
                        <a class="btn btn-xs btn-primary" href="{{ route('bulletin.create') }}">
                            {{ trans('app.添加', ['value' => trans('app.公告信息')]) }}
                        </a>
                    {{--@endif--}}
                </div>
            </div>
            <div class="ibox-content">
                <table class="table table-striped tooltip-demo">
                    <thead>
                        <tr>
                            <th>{{ trans('app.id') }}</th>
                            <th>{{ trans('app.发布人') }}</th>
                            <th>{{ trans('app.内容标题') }}</th>
                            <th>{{ trans('app.发布日期') }}</th>
                            <th>{{ trans('app.有效日期') }}</th>
                            <th>{{ trans('app.发布权重') }}</th>
                            <th>{{ trans('att.操作') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>xxx</td>
                            <td>xxx</td>
                            <td>xxx</td>
                            <td>xxx</td>
                            <td>xxx</td>
                            <td>xxx</td>
                            <td>{!! BaseHtml::tooltip(trans('app.设置'), route('bulletin.edit', ['id' => 1])) !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection