@extends('attendance.side-nav')

@section('title', $title)

@section('content')
    <div class="col-xs-6">
        <div class="row">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>假期天数</h5>
                </div>
                <div class="ibox-content">

                    {!! Form::open(['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}
                    {{--分割线--}}
                    <div>
                        @if(!empty($data))
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>假期名</th>
                                    <th>假期详情</th>
                                    <th>剩余假期天数</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $d)
                                    <tr>
                                        <td>{{ $d['holiday'] }}</td>
                                        <td><pre style="width: 15em; height: 3em" >{{ $d['memo'] }}</pre></td>
                                        <td>{{ $d['num'] . '天'}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <h1>当前未找到假期</h1>
                        @endif
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>近期异常打卡</h5>
                </div>
                <div class="ibox-content">
                    <p><strong>上班异常打卡次数</strong> 0次</p>
                    <p><strong>下班异常打卡次数</strong> 0次</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>我的申请单</h5>
                </div>
                <div class="ibox-content">
                    <p><strong>审核中的申请单</strong> 1张</p>
                    <p><strong>需要我审核的申请</strong> 0张 <a>查看></a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-5">
        <div class="ibox float-e-margins"  style="width: 600px">
            <div class="ibox-title">
                <h5>工作日历</h5>
            </div>
            <div class="ibox-content">
                @include('widget.calendar-user')
            </div>
        </div>
    </div>
@endsection