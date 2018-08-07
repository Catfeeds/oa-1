@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent
    <div class="col-sm-8">
        <div class="title-action">
            @if(Entrust::can(['attendance-all', 'leave.all', 'leave.edit', 'leave.create']))
                <a href="{{ route('leave.create') }}" class="btn btn-primary btn-sm">{{ trans('请假申请') }}</a>
            @endif
        </div>
    </div>

@endsection

@section('content')

    @include('flash::message')

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-striped tooltip-demo">
                            <thead>
                            <tr>
                                <th>{{ trans('att.请假类型') }}</th>
                                <th>{{ trans('att.开始日期') }}</th>
                                <th>{{ trans('att.开始时间') }}</th>
                                <th>{{ trans('att.结束日期') }}</th>
                                <th>{{ trans('att.结束时间') }}</th>
                                <th>{{ trans('att.假期时长') }}</th>
                                <th>{{ trans('att.事由') }}</th>
                                <th>{{ trans('att.操作') }}</th>
                                <th>{{ trans('att.对假期有疑问') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $v)
                                <tr>
                                    <td>{{ $v['name'] }}</td>
                                    <td>{{ $v['name'] }}</td>
                                    <td>{{ $v['name'] }}</td>
                                    <td>{{ $v['sort'] }}</td>
                                    <td>{{ $v['created_at'] }}</td>
                                    <td>{{ $v['updated_at'] }}</td>
                                    <td>
                                        @if(Entrust::can(['attendance-all', 'leave-all', 'leave.edit', 'leave.create']))
                                            {!! BaseHtml::tooltip(trans('app.设置'), route('leave.edit', ['id' => $v['leave_id']]), 'cog fa-lg') !!}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
