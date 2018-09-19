@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent

@endsection

@section('content')

    @include('flash::message')
    @include('widget.scope-staff', ['scope' => $scope])

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
                                <th>{{ trans('staff.员工工号') }}</th>
                                <th>{{ trans('att.姓名') }}</th>
                                <th>{{ trans('staff.部门') }}</th>
                                <th>{{ trans('staff.岗位') }}</th>
                                <th>{{ trans('staff.性别') }}</th>
                                <th>{{ trans('staff.联系电话(手机)') }}</th>
                                <th>{{ trans('staff.在职状态') }}</th>
                                <th>{{ trans('att.操作') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $v)
                                <tr>
                                    <td>{{$v['username']}}</td>
                                    <td>{{$v['alias']}}</td>
                                    <td>{{$dept[$v['dept_id']] ?? ''}}</td>
                                    <td>{{$job[$v['dept_id']] ?? ''}}</td>
                                    <td>{{ \App\Models\UserExt::$sex[$v['userExt']->sex] ?? '未知'}}</td>
                                    <td>{{$v['mobile']}}</td>
                                    <td>{!! \App\User::getStatusText($v['status']) !!}</td>
                                    <td>
                                        @if(Auth::user()->user_id != $v['user_id'] && Entrust::can(['staff-all', 'staff.edit']))
                                            {!! BaseHtml::tooltip(trans('app.设置'), route('staff.edit', ['id' => $v['user_id']]), 'cog fa fa-lg') !!}
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
@include('widget.select2')
