@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent
    <div class="col-sm-8">
        <div class="title-action">
            @if(Entrust::can(['attendance-all', 'daily-detail.all', 'daily-detail.review']))
                <a href="{{ route('daily-detail.review.import') }}" class="btn btn-primary btn-sm">{{ trans('导入打卡记录') }}</a>
                <a href="{{ route('daily-detail.review.info') }}" class="btn btn-success btn-sm">{{ trans('返回') }}</a>
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
                                <th>{{ trans('att.录入名称') }}</th>
                                <th>{{ trans('att.附件') }}</th>
                                <th>{{ trans('att.上传时间') }}</th>
                                <th>{{ trans('att.操作') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $v)
                                <tr>
                                    <td>{{ $v['name'] }}</td>
                                    <td>{{ $v['annex'] }}</td>
                                    <td>{{ $v['created_at'] }}</td>
                                    <td>
                                        @if(Entrust::can(['attendance-all', 'daily-detail.all', 'daily-detail.review']))
                                            {!! BaseHtml::tooltip(trans('att.生成员工每日打卡信息'), route('daily-detail.review.import.generate', ['id' => $v['id']]), 'cog fa fa-newspaper-o') !!}
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