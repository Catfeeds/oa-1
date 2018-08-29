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
                                    <a href="{!! route('reconciliationPool', array_merge(Request::all(), ['source' => $k])) !!}">{{ $v }}</a>
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
                    <div class="stat-view">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-hover table-bordered dataTable"
                                   cellspacing="0"
                                   width="100%">
                                <thead>
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
    {{-- 弹出窗 --}}
    <div class="modal inmodal" id="confirm-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                class="sr-only">Close</span></button>
                    <h4 class="modal-title">{{ trans('crm.差异明细') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped results">
                            <thead>
                            <tr>
                                <th>{{ trans('crm.差异名') }}</th>
                                <th>{{ trans('crm.差异额|差异比例') }}</th>
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
                "ajax": '{!! route('reconciliationPool.data', array_merge(Request::all(), ['product_id' => $pid, 'source' => $source])) !!}',
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

        $(document).on('click', '.fa-eye', function(){
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
