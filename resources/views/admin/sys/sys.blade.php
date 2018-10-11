@extends('attendance.side-nav')

@section('title', $title ?? trans('app.系统设置'))
@section('page-head')
    @parent
@endsection

@section('content')
    @yield('content')
@endsection
