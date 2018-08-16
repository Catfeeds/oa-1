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
                    <div style="width: {{ ($status - 1)*17 }}%;" class="progress-bar"></div>
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
                </div>
                <div class="ibox-content  tooltip-demo">
                    <div class="row">
                        @if(isset($status))
                            @if($source == \App\Models\Crm\Reconciliation::OPERATION)
                                @include('widget.audit-button',['first' => 1, 'second' => 2])
                            @elseif($source == \App\Models\Crm\Reconciliation::ACCRUAL)
                                @include('widget.audit-button',['first' => 3, 'second' => 4])
                            @elseif($source == \App\Models\Crm\Reconciliation::RECONCILIATION)
                                @include('widget.audit-button',['first' => 5, 'second' => 6])
                            @else
                                @include('widget.audit-button',['first' => 0, 'second' => 0])
                            @endif
                        @endif
                    </div>
                    <div class="stat-view">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered table-hover table-bordered" cellspacing="0"
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
@endsection
@include('widget.select2')
@include('widget.datatable')
@include('widget.bootbox')
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
                "order": [[0, "asc"]],
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
    </script>
@endpush
