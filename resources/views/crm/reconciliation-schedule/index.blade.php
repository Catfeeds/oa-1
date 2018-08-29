@extends('crm.side-nav')

@section('title', $title)

@section('page-head')

    @parent

@endsection

@section('content')
    @include('widget.scope-month', ['scope' => $scope])
    @include('flash::message')

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5> {{ $title }} </h5>
                </div>
                <div class="ibox-content  tooltip-demo">
                    <div class="stat-view">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-hover table-bordered dataTable"
                                   cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    @foreach($header as $v)
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
                    <h4 class="modal-title">{{ trans('crm.账龄明细') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped results">
                            <thead>
                            <tr>
                                <th>{{ trans('crm.账龄') }}</th>
                                <th>{{ trans('crm.对账流水') }}</th>
                                <th>{{ trans('crm.已对账流水') }}</th>
                                <th>{{ trans('crm.未对账流水') }}</th>
                                <th>{{ trans('crm.是否逾期') }}</th>
                                <th>{{ trans('crm.开票完成率') }}</th>
                                <th>{{ trans('crm.已开票流水') }}</th>
                                <th>{{ trans('crm.已收款') }}</th>
                                <th>{{ trans('crm.代收款') }}</th>
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
                "ajax": '{!! route('reconciliationSchedule.data', array_merge(Request::all(), ['product_id' => $pid])) !!}',
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
                    html += '<tr>' +
                        '<td>' + v.typ + '</td>' +
                        '<td>' + v.rmb + '</td>' +
                        '<td>' + v.second_rmb + '</td>' +
                        '<td>' + v.first_rmb + '</td>' +
                        '<td>' + v.overdue + '</td>' +
                        '<td>' + v.billing_rate + '</td>' +
                        '<td>' + v.invoices_rmb + '</td>' +
                        '<td>' + v.payback_rmb + '</td>' +
                        '<td>' + v.not_payback_rmb + '</td>' +
                        '</tr>';
                });
                $('.results > tbody').html(html);
                $('#confirm-modal').modal('show');
            });
        });
    </script>
@endpush
