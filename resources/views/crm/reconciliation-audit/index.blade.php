@extends('crm.side-nav')

@section('title', $title)

@section('page-head')

    @parent

@endsection

@section('content')
    @include('widget.scope-month', ['scope' => $scope])
    @include('flash::message')
    @if(isset($status))
        <div class="col-lg-4">
            <dt>进度条:</dt>
            <dd>
                <div class="progress progress-striped active m-b-sm" style="background-color: white">
                    <div style="width: {{ ($status - 1)*13 }}%;" class="progress-bar"></div>
                </div>
                <small>审核进度： <strong>{{ \App\Models\Crm\Reconciliation::REVIEW_TYPE[$status] }}</strong></small>
            </dd>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <div class="panel blank-panel">

                <div class="panel-heading">
                    <div class="panel-options">

                        <ul class="nav nav-tabs">
                            @foreach($review as $k => $v)
                                <li @if($k == $source) class="active" @endif>
                                    <a href="{!! route('reconciliationAudit', array_merge(Request::all(), ['source' => $k])) !!}">{{ $v }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5> {{ $title }} </h5>
                    @if($source != \App\Models\Crm\Reconciliation::ALL)
                        <div class="html5buttons"
                             style="height: 30px!important; float: left!important; margin-left: 20px!important;">
                            <div class="dt-buttons btn-group">
                                <a class="btn btn-default buttons-copy buttons-html5" tabindex="0"
                                   aria-controls="example"
                                   href="{!! route('reconciliationAudit', array_merge(Request::all(), ['source' => $source])) !!}"><span>明细</span></a>
                                <a class="btn btn-default buttons-csv buttons-html5" tabindex="0"
                                   aria-controls="example"
                                   href="{!! route('reconciliationPool', array_merge(Request::all(), ['source' => $source])) !!}"><span>总览</span></a>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="ibox-content  tooltip-demo">
                    <div class="row">
                        @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit', 'reconciliation-reconciliationAudit.invoice', 'reconciliation-reconciliationAudit.invoice', 'reconciliation-reconciliationAudit.review']))
                            @if($source == \App\Models\Crm\Reconciliation::UNRD)
                                @include('widget.button',['btn' => [
                                    ['ops-submit-btn', '提交审核', 'btn-info', in_array(\App\Models\Crm\Principal::OPS, $limitPost) && $status == \App\Models\Crm\Reconciliation::UNRD],
                                    ], 'isCheck' => false])
                            @elseif($source == \App\Models\Crm\Reconciliation::OPS)
                                @include('widget.button',['btn' => [
                                    ['opd-submit-btn', '通过审核', 'btn-info', in_array(\App\Models\Crm\Principal::OPD, $limitPost)&& $status == \App\Models\Crm\Reconciliation::OPS],
                                    ['opd-refuse-btn', '拒绝审核', 'btn-danger', in_array(\App\Models\Crm\Principal::OPD, $limitPost)&& $status == \App\Models\Crm\Reconciliation::OPS],
                                    ], 'isCheck' => false])
                            @elseif($source == \App\Models\Crm\Reconciliation::OPD)
                                @include('widget.button',['btn' => [
                                    ['fac-submit-btn', '提交审核', 'btn-info', in_array(\App\Models\Crm\Principal::FAC, $limitPost)&& $status == \App\Models\Crm\Reconciliation::OPD],
                                    ['fac-refuse-btn', '拒绝审核', 'btn-danger', in_array(\App\Models\Crm\Principal::FAC, $limitPost)&& $status == \App\Models\Crm\Reconciliation::OPD],
                                    ], 'isCheck' => false])
                            @elseif($source == \App\Models\Crm\Reconciliation::FAC)
                                @include('widget.button',['btn' => [
                                    ['treasurer-submit-btn', '通过审核', 'btn-info', in_array(\App\Models\Crm\Principal::TREASURER, $limitPost)&& $status == \App\Models\Crm\Reconciliation::FAC],
                                    ['treasurer-refuse-btn', '拒绝审核', 'btn-danger', in_array(\App\Models\Crm\Principal::TREASURER, $limitPost)&& $status == \App\Models\Crm\Reconciliation::FAC],
                                    ], 'isCheck' => false])
                            @elseif($source == \App\Models\Crm\Reconciliation::TREASURER)
                                @include('widget.button',['btn' => [
                                    ['frc-submit-btn', '提交审核', 'btn-info', in_array(\App\Models\Crm\Principal::FRC, $limitPost) && $status == \App\Models\Crm\Reconciliation::TREASURER],
                                    ['frc-invoice-btn', '确认开票', 'btn-primary', in_array(\App\Models\Crm\Principal::FRC, $limitPost)],
                                    ['frc-pay-btn', '确认回款', 'btn-success', in_array(\App\Models\Crm\Principal::FRC, $limitPost)],
                                    ['frc-wipe-btn', '一键抹零', 'btn-danger', in_array(\App\Models\Crm\Principal::FRC, $limitPost)],
                                    ], 'isCheck' => true])
                            @elseif($source == \App\Models\Crm\Reconciliation::FRC)
                                @include('widget.button',['btn' => [
                                    ['ops-review-btn', '通过复核', 'btn-info', in_array(\App\Models\Crm\Principal::OPS, $limitPost)],
                                    ['ops-refuse-btn', '拒绝复核', 'btn-danger', in_array(\App\Models\Crm\Principal::OPS, $limitPost)],
                                    ], 'isCheck' => true])
                            @elseif($source == \App\Models\Crm\Reconciliation::OOR)
                                @include('widget.button',['btn' => [
                                    ['fsr-submit-btn', '通过审核', 'btn-info', in_array(\App\Models\Crm\Principal::FSR, $limitPost)],
                                    ['fsr-refuse-btn', '拒绝审核', 'btn-danger', in_array(\App\Models\Crm\Principal::FSR, $limitPost)],
                                    ], 'isCheck' => true])
                            @endif
                        @endif
                    </div>
                    <div class="stat-view">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-hover table-bordered dataTable"
                                   cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    @if(in_array($source, [\App\Models\Crm\Reconciliation::UNRD, \App\Models\Crm\Reconciliation::OPS]))
                                        <th colspan="16" class="red-color">基本数据</th>
                                        <th colspan="2" class="orange-color">后台流水</th>
                                        <th colspan="6" class="yellow-color">运营调整</th>
                                        <th colspan="2" class="green-color">运营流水</th>
                                        <th colspan="1">--</th>
                                    @elseif(in_array($source, [\App\Models\Crm\Reconciliation::OPD, \App\Models\Crm\Reconciliation::FAC]))
                                        <th colspan="16" class="red-color">基本数据</th>
                                        <th colspan="4" class="orange-color">分成费率</th>
                                        <th colspan="2" class="yellow-color">运营流水</th>
                                        <th colspan="6" class="green-color">计提调整</th>
                                        <th colspan="4" class="blue-color">计提流水分成</th>
                                        <th colspan="1">--</th>
                                    @elseif(in_array($source, [\App\Models\Crm\Reconciliation::TREASURER, \App\Models\Crm\Reconciliation::FRC, \App\Models\Crm\Reconciliation::OOR]))
                                        <th colspan="17" class="red-color">基本数据</th>
                                        <th colspan="4" class="blue-color">开票信息</th>
                                        <th colspan="3" class="red-color">回款信息</th>
                                        <th colspan="4" class="orange-color">分成费率</th>
                                        <th colspan="2" class="yellow-color">计提流水</th>
                                        <th colspan="6" class="green-color">对账调整</th>
                                        <th colspan="4" class="blue-color">对账流水分成</th>
                                        <th colspan="1">--</th>
                                    @else
                                        <th colspan="16" class="red-color">基本数据</th>
                                        <th colspan="4" class="orange-color">分成费率</th>
                                        <th colspan="4" class="yellow-color">后台流水分成</th>
                                        <th colspan="6" class="green-color">运营调整</th>
                                        <th colspan="4" class="blue-color">运营流水分成</th>
                                        <th colspan="6" class="indigo-color">计提调整</th>
                                        <th colspan="4" class="violet-color">计提流水分成</th>
                                        <th colspan="6" class="red-color">对账调整</th>
                                        <th colspan="4" class="orange-color">对账流水分成</th>
                                    @endif
                                </tr>
                                <tr>
                                    @foreach($columns as $v)
                                        <th>{{ $v }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal inmodal" id="confirm-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                class="sr-only">Close</span></button>
                    <h4 class="modal-title">{{ trans('crm.调整明细') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped results">
                            <thead>
                            <tr>
                                <th>{{ trans('crm.调整类型') }}</th>
                                <th>{{ trans('crm.调整') }}</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">{{ trans('crm.关闭') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('widget.icheck')
@include('widget.select2')
@include('widget.datatable')
@include('widget.bootbox')
@include('widget.gm-batch-operation')
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#example').dataTable({
                "ajax": '{!! route('reconciliationAudit.data', array_merge(Request::all(), ['product_id' => $pid, 'source' => $source])) !!}',
                language: {
                    url: '{{ asset('js/plugins/dataTables/i18n/Chinese.json') }}'
                },
                bLengthChange: false,
                paging: false,
                info: false,
                searching: false,
                fixedHeader: true,
                "order": [[1, "asc"]],
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    {extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel'}
                ]
            });
        });

        $('#frc-invoice-btn').batch({
            url: '{!! route('reconciliationAudit.invoice') !!}',
            selector: '.i-checks:checked',
            type: '3',
            alert_confirm: '确定要批量开票吗？'
        });
        $('#frc-pay-btn').batch({
            url: '{!! route('reconciliationAudit.payback') !!}',
            selector: '.i-checks:checked',
            type: '4',
            alert_confirm: '确定要批量确认回款吗？'
        });

        $('#frc-wipe-btn').batch({
            url: '{!! route('reconciliationAudit.wipe') !!}',
            selector: '.i-checks:checked',
            type: '1',
            alert_confirm: '确定要批量一键抹零吗？'
        });

        //审核按键
        $('#ops-submit-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::OPS, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '2',
            alert_confirm: '确定要批量提交审核吗？'
        });

        $('#opd-submit-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::OPD, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '2',
            alert_confirm: '确定要批量通过审核吗？'
        });
        $('#opd-refuse-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::UNRD, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '2',
            alert_confirm: '确定要批量拒绝审核吗？'
        });
        $('#fac-submit-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::FAC, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '2',
            alert_confirm: '确定要批量提交审核吗？'
        });
        $('#fac-refuse-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::OPS, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '2',
            alert_confirm: '确定要批量拒绝审核吗？'
        });
        $('#treasurer-submit-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::TREASURER, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '2',
            alert_confirm: '确定要批量通过审核吗？'
        });
        $('#treasurer-refuse-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::OPD, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '2',
            alert_confirm: '确定要批量拒绝审核吗？'
        });
        $('#frc-submit-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::FRC, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '5',
            alert_confirm: '确定要批量提交审核吗？'
        });
        $('#ops-review-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::OOR, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '5',
            alert_confirm: '确定要批量通过复核吗？'
        });
        $('#ops-refuse-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::TREASURER, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '5',
            alert_confirm: '确定要批量拒绝复核吗？'
        });
        $('#fsr-submit-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::FSR, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '5',
            alert_confirm: '确定要批量通过审核吗？'
        });
        $('#fsr-refuse-btn').batch({
            url: '{!! route('reconciliationAudit.review', array_merge(['status' => \App\Models\Crm\Reconciliation::FRC, 'pid' => $pid, 'source' => $source], Request::all())) !!}',
            selector: '.i-checks:checked',
            type: '5',
            alert_confirm: '确定要批量拒绝审核吗？'
        });

        $(document).on('click', '.generate', function (event) {
            event.preventDefault();
            var href = $(this).is('a') ? $(this).attr('href') : $(this).parent('a').attr('href');

            bootbox.prompt({
                title: "调整数量",
                inputType: 'number',
                callback: function (num) {
                    if (num) {
                        if (num) {
                            var data = {
                                num: num
                            };
                            window.location = href + '&' + $.param(data);
                        }
                    }
                }
            });
        });

        $(document).on('click', '.eye', function(){
            var url = $(this).data('url');
            console.log(url);
            $.getJSON(url, function (res) {
                var html = '';
                $.each(res, function (k, v) {
                    html += '<tr><td>' + v.type + '</td><td>' + v.adjustment + '</td></tr>';
                });
                $('.results > tbody').html(html);
                $('#confirm-modal').modal('show');
            });
        });
    </script>
@endpush
