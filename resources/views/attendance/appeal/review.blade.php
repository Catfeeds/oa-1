@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent
@endsection

@push('css')
<style type="text/css">
    .paddingx-5 {
        padding: 0 5px;
    }
</style>
@endpush

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row m-b-md">
            <div class="col-xs-2 paddingx-5">
                <div class="widget white-bg">
                    <div class="m-b-md">
                        <h1 class="no-margins">待处理申诉</h1>
                    </div>
                    <div class="text-center">
                        <p class="font-bold no-margins text-success" style="font-size: 50px">{{ $countPending }}</p>
                    </div>
                </div>
            </div>

            <div class="col-xs-2 paddingx-5">
                <div class="widget white-bg">
                    <div class="m-b-md">
                        <h1 class="no-margins">已处理申诉</h1>
                    </div>
                    <div class="text-center">
                        <p class="font-bold no-margins text-success" style="font-size: 50px">{{ $countComplete }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 paddingx-5">
                <div class="white-bg">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>部门</th>
                                <th>工号</th>
                                <th>姓名</th>
                                <th>申诉类型</th>
                                <th>申诉内容</th>
                                <th>提交时间</th>
                                <th>处理结果</th>
                                <th>操作人</th>
                                <th>处理时间</th>
                                <th>备注</th>
                                <th width="5%">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appeals as $appeal)
                                <tr>
                                    <td>{{ $deptList[$appeal['users']['dept_id']] }}</td>
                                    <td>{{ $appeal['users']['username'] }}</td>
                                    <td>{{ $appeal['users']['alias'] }}</td>
                                    <td>
                                        @if($appeal['appeal_type'] == \App\Models\Attendance\Appeal::APPEAL_LEAVE)
                                            {{ \App\Models\Sys\HolidayConfig::$applyType[$appeal['apply_type_id']] }}
                                        @endif
                                        @if($appeal['appeal_type'] == \App\Models\Attendance\Appeal::APPEAL_DAILY)
                                            每日明细
                                        @endif
                                    </td>
                                    <td>
                                        <pre style="height: 5em;width: 20em">{{ $appeal['reason'] }}</pre>
                                    </td>
                                    <td>{{ $appeal['created_at'] }}</td>
                                    <td>{{ \App\Models\Attendance\Appeal::getTextArr()[$appeal['result']] }}</td>
                                    <td>{{ $operateUser[$appeal['operate_user_id'] ?? ''] ?? '未操作' }}</td>
                                    <td>
                                        {{ $appeal['result'] == 0 ? '未处理' : $appeal['updated_at'] }}
                                    </td>
                                    <td>
                                        <pre style="height: 5em;width: 20em">{{ $appeal['remark'] ?? '暂无' }}</pre>
                                    </td>
                                    <td>
                                        <a data-toggle="modal" data-target="#exampleModal"
                                           data-whatever="{{ $appeal['id'] }}" data-remark="{{ $appeal['remark'] }}" data-result="{{ $appeal['result'] }}">
                                            {{ $appeal['result'] == 0 ? '处理' : '修改' }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('widget.appeal-modal-admin')

@endsection