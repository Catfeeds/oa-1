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
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-hover table-bordered" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                @foreach($columns as $v)
                                    <th>{{ $v['title'] }}</th>
                                @endforeach
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('widget.select2')
@include('widget.datatable')
@push('scripts')
<script>
    $(document).ready(function () {
        $('#example').dataTable({
            "ajax": '{!! route('reconciliationPrincipal.data', \Request::all()) !!}',
            language: {
                url: '{{ asset('js/plugins/dataTables/i18n/Chinese.json') }}'
            },
            "searching": false,
            "fixedHeader": true,
            "aLengthMenu": [15, 30],
            "iDisplayLength": 15,
            "order": [[0, "asc"]],
            "columnDefs": [
                {
                    "targets": 8,
                    "data": "opt",
                    "render": function (data, type, full, meta) {
                        return '<a href="{{ route('reconciliationPrincipal.edit') }}' + '?' + 'pid=' + full[0] + '">编辑</a>';
                    }
                }
            ]
        });
    });
</script>
@endpush
