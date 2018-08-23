@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent
    <div class="col-sm-8">
        <div class="title-action">
            @if(Entrust::can(['attendance-all', 'daily-detail.all', 'daily-detail.review']))
                <a href="{{ route('daily-detail.review.import.info') }}" class="btn btn-success btn-sm">{{ trans('导入打卡记录列表') }}</a>
                <a href="{{ route('daily-detail.review.import') }}" class="btn btn-primary btn-sm">{{ trans('导入打卡记录') }}</a>
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
                                <th>{{ trans('att.基础信息') }}</th>
                                <th>{{ trans('att.考勤天数') }}</th>
                                <th>{{ trans('att.扣分统计') }}</th>
                                <th>{{ trans('att.剩余假期') }}</th>
                                <th>{{ trans('att.操作') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $v)
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
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