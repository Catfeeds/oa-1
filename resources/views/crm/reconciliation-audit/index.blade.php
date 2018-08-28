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
                <small>审核进度： <strong>{{ \App\Models\Crm\Reconciliation::REVIEW_TYPE[$status+1] }}</strong></small>
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
                        @if(isset($status))
                            @if($source == \App\Models\Crm\Reconciliation::OPERATION)
                                @include('widget.audit-button',['first' => 1, 'second' => 2, 'third' => 0])
                            @elseif($source == \App\Models\Crm\Reconciliation::ACCRUAL)
                                @include('widget.audit-button',['first' => 3, 'second' => 4, 'third' => 0])
                            @elseif($source == \App\Models\Crm\Reconciliation::RECONCILIATION)
                                @include('widget.button',['btn' => [['invoice-btn', '确认开票', 'btn-primary'],['pay-btn', '确认回款', 'btn-success']]])
                            @elseif($source == \App\Models\Crm\Reconciliation::ALL)
                                @include('widget.audit-button',['first' => 0, 'second' => 0, 'third' => 0])
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
                                    @if($source == \App\Models\Crm\Reconciliation::OPERATION)
                                        <th colspan="16" class="red-color">基本数据</th>
                                        <th colspan="2" class="orange-color">后台流水</th>
                                        <th colspan="6" class="yellow-color">运营调整</th>
                                        <th colspan="2" class="green-color">运营流水</th>
                                        <th colspan="1">--</th>
                                    @elseif($source == \App\Models\Crm\Reconciliation::ACCRUAL)
                                        <th colspan="16" class="red-color">基本数据</th>
                                        <th colspan="4" class="orange-color">分成费率</th>
                                        <th colspan="2" class="yellow-color">运营流水</th>
                                        <th colspan="6" class="green-color">计提调整</th>
                                        <th colspan="4" class="blue-color">计提流水分成</th>
                                        <th colspan="1">--</th>
                                    @elseif($source == \App\Models\Crm\Reconciliation::RECONCILIATION)
                                        <th colspan="17" class="red-color">基本数据</th>
                                        <th colspan="4" class="blue-color">开票信息</th>
                                        <th colspan="3" class="red-color">回款信息</th>
                                        <th colspan="4" class="orange-color">分成费率</th>
                                        <th colspan="2" class="yellow-color">计提流水</th>
                                        <th colspan="6" class="green-color">对账调整</th>
                                        <th colspan="4" class="blue-color">对账流水分成</th>
                                        <th colspan="1">--</th>
                                    @elseif($source == \App\Models\Crm\Reconciliation::ALL)
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
        $('.btn-audit').click(function () {
            $("#warning_button").attr('disabled', true);
            $("#button").attr('disabled', true);
            $("#back_go").attr('disabled', true);
            $("#tj_button").attr('disabled', true);
            $("#warning_button_tow").attr('disabled', true);
        });

        $('#invoice-btn').batch({
            url: '{!! route('reconciliationAudit.invoice') !!}',
            selector: '.i-checks:checked',
            type: '3',
            alert_confirm: '确定要拒绝提审吗？'
        });
        $('#pay-btn').batch({
            url: '{!! route('reconciliationAudit.payback') !!}',
            selector: '.i-checks:checked',
            type: '4',
            alert_confirm: '确定要拒绝提审吗？'
        });
    </script>
@endpush
