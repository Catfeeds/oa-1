@extends('layouts.top-nav')

@section('title', $title ?? trans('app.系统设置'))
@section('body-class', 'top-navigation')

@section('top-nav')

    @yield('content')

@endsection
