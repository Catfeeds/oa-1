@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent

    <div class="col-sm-8">
        <div class="title-action">
            @if(Entrust::can(['entry.create']))
                <a href="{{ route('entry.create') }}" class="btn btn-primary btn-sm">{{ trans('staff.添加待入职') }}</a>
            @endif
        </div>
    </div>

@endsection

@section('content')

    @include('flash::message')
        @include('widget.scope-entry', ['scope' => $scope])

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
                                <th>{{ trans('att.姓名') }}</th>
                                <th>{{ trans('app.部门') }}</th>
                                <th>{{ trans('staff.岗位类型') }}</th>
                                <th>{{ trans('staff.预计入职日期') }}</th>
                                <th>{{ trans('app.状态') }}</th>
                                <th>{{ trans('staff.办理操作') }}</th>
                                <th>{{ trans('att.操作') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $v)
                                <tr>
                                    <td>{{ $v['name'] }}</td>
                                    <td>{{ $dept[$v['dept_id']] ?? '--' }}</td>
                                    <td>{{ $job[$v['job_id']] ?? '--' }}</td>
                                    <td>{{ $v['entry_time']}}</td>
                                    <td>{{ \App\Models\StaffManage\Entry::$status[$v['status']] ?? '--' }}</td>
                                    <td>
                                        @if(Entrust::can(['entry.review']))
                                            {!! BaseHtml::tooltip(trans('staff.入职信息确认'), route('entry.showInfo', ['id' => $v['entry_id']]), ' fa-check-square-o text-primary fa-lg') !!}
                                        @endif

                                        @if(Entrust::can(['entry.review']) && !in_array($v['status'], [\App\Models\StaffManage\Entry::REVIEW_PASS, \App\Models\StaffManage\Entry::REVIEW_REFUSE]))
                                            {!! BaseHtml::tooltip(trans('staff.放弃入职'), route('entry.refuse', ['id' => $v['entry_id']]), ' fa-times-circle-o text-danger fa-lg confirmation', ['data-confirm' => trans('staff.确认放弃办理入职?')]) !!}
                                        @endif
                                    </td>
                                    <td>
                                        @if(Entrust::can(['entry.sendMail']) && !in_array($v['status'], [\App\Models\StaffManage\Entry::REVIEW_PASS, \App\Models\StaffManage\Entry::REVIEW_REFUSE]))
                                            {!! BaseHtml::tooltip(trans('staff.发送入职邀请'), route('entry.createSendInfo', ['id' => $v['entry_id']]), ' fa-send-o text-info fa-lg confirmation', ['data-confirm' => trans('staff.确认发送入职邀请?')]) !!}
                                        @endif

                                        @if(Entrust::can(['entry.edit']) && !in_array($v['status'], [\App\Models\StaffManage\Entry::REVIEW_PASS, \App\Models\StaffManage\Entry::REVIEW_REFUSE]))
                                           {!! BaseHtml::tooltip(trans('staff.调整入职信息'), route('entry.edit', ['id' => $v['entry_id']]), ' fa-cog text-success fa-lg') !!}
                                        @endif

                                        @if(Entrust::can(['entry.del']) && !in_array($v['status'], [\App\Models\StaffManage\Entry::REVIEW_PASS]))
                                            <div class="btn-group">
                                                <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle"><span class="caret"></span></button>
                                                <ul style="min-width: 60px;max-height: 40px" class="dropdown-menu">
                                                    <li style="width: 1em">{!! BaseHtml::tooltip(trans('staff.删除'), route('entry.del', ['id' => $v['entry_id']]), ' fa-times text-danger fa-lg confirmation', ['data-confirm' => trans('staff.确认删除该员工入职信息?')]) !!}</li>
                                                </ul>
                                            </div>
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
@include('widget.bootbox')
@include('widget.review-batch-operation')

