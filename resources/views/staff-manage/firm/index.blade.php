@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent
    <div class="col-sm-8">
        <div class="title-action">
            @if(Entrust::can(['firm-all', 'firm.edit', 'firm.create']))
                <a href="{{ route('firm.create') }}" class="btn btn-primary btn-sm">{{ trans('staff.添加公司') }}</a>
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
                                <th>{{ trans('staff.公司ID') }}</th>
                                <th>{{ trans('staff.公司名称') }}</th>
                                <th>{{ trans('staff.公司别名') }}</th>
                                <th>{{ trans('staff.创建时间') }}</th>
                                <th>{{ trans('att.操作') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $v)
                                <tr>
                                    <td>{{ $v['firm_id'] }}</td>
                                    <td>{{ $v['firm'] }}</td>
                                    <td>{{ $v['alias'] }}</td>
                                    <td>{{ $v['created_at'] }}</td>
                                    <td>
                                        @if(Entrust::can(['firm-all', 'firm.create', 'firm.edit']))
                                            {!! BaseHtml::tooltip(trans('app.设置'), route('firm.edit', ['id' => $v['firm_id']]), 'cog fa fa-search') !!}
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