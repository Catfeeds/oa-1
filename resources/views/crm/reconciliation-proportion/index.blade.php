@extends('crm.side-nav')

@section('title', $title)

@section('page-head')

    @parent
    <div class="col-sm-8">
        <div class="title-action">
            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationProportion', 'reconciliation-reconciliationProportion.batch']))
                <a href="{{ route('reconciliationProportion.batch') }}"
                   class="btn btn-primary btn-sm">批量添加比例</a>
            @endif
        </div>
    </div>
@endsection

@section('content')
    @include('widget.scope-month', ['scope' => $scope])
    @include('flash::message')
    <div class="row">
        <div class="col-lg-12">
            <div class="panel blank-panel">

                <div class="panel-heading">
                    <div class="panel-options">

                        <ul class="nav nav-tabs">
                            @foreach($product as $k => $v)
                                <li @if($k == $pid) class="active" @endif>
                                    <a href="{!! route('reconciliationProportion', array_merge(Request::all(), ['pid' => $k])) !!}">{{ $v }}</a>
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

                    </div>
                    <div class="stat-view">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-hover table-bordered dataTable" cellspacing="0"
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
                "ajax": '{!! route('reconciliationProportion.data', array_merge(Request::all(), ['pid' => $pid])) !!}',
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
    </script>
@endpush
