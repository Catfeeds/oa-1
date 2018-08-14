@extends('layouts.top-nav')

@section('title', trans('首页'))

@section('content')
    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="wrapper wrapper-content animated fadeInRight">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="contact-box">
                                <a href="{{route('attIndex')}}">
                                    <div class="col-sm-4">
                                        <div class="text-center">
                                            <img alt="image" class="img-circle m-t-xs img-responsive"
                                                 src="img/att.png">
                                            <div class="m-t-xs font-bold">考勤系统</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <h3><strong>考勤系统后台</strong></h3>
                                        <p><i class="fa fa-map-marker"></i>考勤相关：请假、打卡等</p>
                                        <address>
                                            <strong></strong><br>
                                            <br>
                                            <br>
                                            <abbr></abbr>
                                        </address>
                                    </div>
                                    <div class="clearfix"></div>
                                </a>
                            </div>
                        </div>
                        {{--@if(Entrust::can(['cms-all', 'cmsCategories.*, cmsArticles.*']))--}}
                        <div class="col-lg-4">
                            <div class="contact-box">
                                <a href="#">
                                    <div class="col-sm-4">
                                        <div class="text-center">
                                            <img alt="image" class="img-circle m-t-xs img-responsive"
                                                 src="img/hr.png">
                                            <div class="m-t-xs font-bold">人事系统</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <h3><strong>人事系统管理</strong></h3>
                                        <p><i class="fa fa-map-marker"></i> 人事相关：招聘，培训等</p>
                                        <address>
                                            <strong></strong><br>
                                            <br>
                                            <br>
                                            <abbr></abbr>
                                        </address>
                                    </div>
                                    <div class="clearfix"></div>
                                </a>
                            </div>
                        </div>
                        {{--@endif--}}

                        <div class="col-lg-4">
                            <div class="contact-box">
                                <a href="{{route('CrmIndex')}}">
                                    <div class="col-sm-4">
                                        <div class="text-center">
                                            <img alt="image" class="img-circle m-t-xs img-responsive"
                                                 src="img/crm.png">
                                            <div class="m-t-xs font-bold">CRM系统</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <h3><strong>CRM系统管理</strong></h3>
                                        <p><i class="fa fa-map-marker"></i> CRM：对账，客户管理</p>
                                        <address>
                                            <strong></strong><br>
                                            <br>
                                            <br>
                                            <abbr></abbr>
                                        </address>
                                    </div>
                                    <div class="clearfix"></div>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.contact-box').each(function () {
            animationHover(this, 'pulse');
        });
    });
</script>
@endpush
