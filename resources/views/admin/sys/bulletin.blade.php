@extends('admin.sys.sys')

@push('css')
<link rel="stylesheet" href="{{ asset('css/plugins/switchery/switchery-0.8/switchery.css') }}" />
@endpush

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="ibox">
            <div class="ibox-title">
                <h5>公告发布显示列表</h5>
                <div class="ibox-tools">
                    @if(Entrust::can(['bulletin.create']))
                        <a class="btn btn-xs btn-primary" href="{{ route('bulletin.create') }}">
                            {{ trans('app.添加', ['value' => trans('app.公告信息')]) }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="ibox-content">
                <table class="table table-striped tooltip-demo">
                    <thead>
                        <tr>
                            <th>{{ trans('app.用户ID') }}</th>
                            <th>{{ trans('app.发布人') }}</th>
                            <th>{{ trans('app.内容标题') }}</th>
                            <th>{{ trans('app.发布日期') }}</th>
                            <th>{{ trans('app.开始时间') }}</th>
                            <th>{{ trans('app.结束时间') }}</th>
                            <th>{{ trans('app.发布权重') }}</th>
                            @if(Entrust::can(['bulletin.changeShow']))
                                <th>{{ trans('att.是否开启') }}</th>
                            @endif
                            @if(Entrust::can(['bulletin.edit']))
                                <th>{{ trans('att.操作') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $v)
                        <tr>
                            <td>{{ $v->id }}</td>
                            <td>{{ $v->send_user }}</td>
                            <td>{{ $v->title }}</td>
                            <td>{{ $v->created_at }}</td>
                            <td>{{ $v->start_date }}</td>
                            <td>{{ $v->end_date }}</td>
                            <td>{{ $v->weight }}</td>
                            @if(Entrust::can(['bulletin.changeShow']))
                                <td><input type="checkbox" id="switch-{{ $v->id }}" class="js-switch" @if($v->show === 1) checked @endif></td>
                            @endif
                            @if(Entrust::can(['bulletin.edit']))
                                <td>{!! BaseHtml::tooltip(trans('app.设置'), route('bulletin.edit', ['id' => $v->id])) !!}</td>
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

@push('scripts')
<script src="{{ asset('js/plugins/switchery/switchery-0.8/switchery.js') }}"></script>
<script type="text/javascript">
    var switchery = [];
    var show = '';
    $('.js-switch').each(function (i, ele) {
        switchery[i] = new Switchery(ele, {
            size: 'small',
            color: '#fa6490'
        });
    }).on('change', function () {
        show = $(this).prop('checked') === true ? 1 : 0;
        $.get("{{ route('bulletin.changeShow') }}", {id: $(this).attr('id').substr(7), show: show}, function (data) {
            if (data == 'success')
                alert('操作成功');
            else {
                alert('操作失败');
            }
        });
    });
</script>
@endpush