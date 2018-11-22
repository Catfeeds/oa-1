@extends('layouts.base')

@push('css')
<style>
    title {
        text-align: center;
    }
    #back {
        position: absolute;
        right: 1px;
    }
    #back-to-top {
        cursor: pointer;
        position: fixed;
        bottom: 20px;
        right: 20px;
        display:none;
    }
</style>
@endpush

@section('body-class', 'gray-bg')

@section('base')

    <div id="wrapper">

        <div class="gray-bg">
            <div class="row wrapper border-bottom white-bg page-heading" id="back-top">

                @section('page-head')

                    <div class="col-sm-12 tooltip-demo">
                        <h2 style="text-align: center; position: relative">
                            {{ $title }}
                            {!! BaseHtml::about(Route::currentRouteName()) !!}
                            <a href="{{ route('home') }}" class="btn btn-danger btn-sm m-r-md" id="back">{{ trans('att.返回首页') }}</a>
                        </h2>
                    </div>
                @show

            </div>
            <div class="row wrapper wrapper-content animated fadeInRight">

                <meta name="referrer" content="no-referrer"/>
                <div class="wrapper wrapper-content">
                    <div class="container">
                        <div class="col-sm-6 col-sm-offset-3">
                            {!! $content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a id="back-to-top" href="#" class="btn btn-success btn-lg back-to-top btn-sm" role="button" data-toggle="tooltip" data-placement="left">
            <span class="glyphicon glyphicon-chevron-up"></span></a>
    </div>

@endsection

@push('scripts')
<script>
    $(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });

        $('#back-to-top').click(function () {
            $('#back-to-top').tooltip('hide');
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });

        $('#back-to-top').tooltip('show');
    });
</script>
@endpush
