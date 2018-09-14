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
        @include('widget.scope-date', ['scope' => $scope])
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                @include('attendance.show-month')
            </div>
        </div>
    </div>
@endsection
