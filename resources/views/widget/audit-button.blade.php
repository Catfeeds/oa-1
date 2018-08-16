@if($status == 7 && Entrust::can(['crm-all', 'reconciliation-all']))
    <div class="col-sm-1 m-b-xs" style="width: 75px;">
        <button class="btn btn-warning btn-sm btn-audit" id="back_go"
                data-toggle="tooltip"
                title="返结账"
                data-original-title="返结账"> 返结账
        </button>
    </div>
@else
    @if($status == $first && in_array($first, $limitPost))
        @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit', 'reconciliation-reconciliationAudit.submitReview']))
            <div class="col-sm-1 m-b-xs" style="width: 75px;">
                <button class="btn btn-info btn-sm btn-audit" id="tj_button"
                        data-toggle="tooltip"
                        title="提交审核"
                        data-original-title="提交审核"> 提交审核
                </button>
            </div>
        @endif
        @if($first > 1)
            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit', 'reconciliation-reconciliationAudit.refuse']))
                <div class="col-sm-1 m-b-xs" style="width: 75px;">
                    <button class="btn btn-warning btn-sm btn-audit" id="warning_button"
                            data-toggle="tooltip"
                            title="拒绝"
                            data-original-title="拒绝"> 拒绝
                    </button>
                </div>
            @endif
        @endif
    @elseif($status == $second && in_array($second, $limitPost))
        @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit', 'reconciliation-reconciliationAudit.review']))
            <div class="col-sm-1 m-b-xs" style="width: 55px;">
                <button class="btn btn-success btn-sm btn-audit" id="button"
                        data-toggle="tooltip"
                        title="审核"
                        data-original-title="审核"> 审核
                </button>
            </div>
        @endif
        @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit', 'reconciliation-reconciliationAudit.refuse']))
            <div class="col-sm-1 m-b-xs" style="width: 75px;">
                <button class="btn btn-warning btn-sm btn-audit" id="warning_button_tow"
                        data-toggle="tooltip"
                        title="拒绝"
                        data-original-title="拒绝"> 拒绝
                </button>
            </div>
        @endif
    @endif
@endif

@include('widget.gm-batch-operation')
@push('scripts')
    <script>
        $(function () {
            $('#back_go').batch({
                url: '{!! route('reconciliationAudit.review', array_merge(Request::all(), ['status' => $status - 1, 'pid' => $pid, 'source' => $source])) !!}',
                selector: '.i-checks:checked',
                type: '2',
                alert_confirm: '确定要返结账吗？'
            });
            $('#tj_button').batch({
                url: '{!! route('reconciliationAudit.review', array_merge(Request::all(), ['status' => $first + 1, 'pid' => $pid, 'source' => $source])) !!}',
                selector: '.i-checks:checked',
                type: '2',
                alert_confirm: '确定要提交审核吗？'
            });
            $('#warning_button').batch({
                url: '{!! route('reconciliationAudit.review', array_merge(Request::all(), ['status' => $first - 1, 'pid' => $pid, 'source' => $source])) !!}',
                selector: '.i-checks:checked',
                type: '2',
                alert_confirm: '确定要拒绝提审吗？'
            });
            $('#button').batch({
                url: '{!! route('reconciliationAudit.review', array_merge(Request::all(), ['status' => $second + 1, 'pid' => $pid, 'source' => $source])) !!}',
                selector: '.i-checks:checked',
                type: '2',
                alert_confirm: '确定要通过提审吗？'
            });
            $('#warning_button_tow').batch({
                url: '{!! route('reconciliationAudit.review', array_merge(Request::all(), ['status' => $second - 1, 'pid' => $pid, 'source' => $source])) !!}',
                selector: '.i-checks:checked',
                type: '2',
                alert_confirm: '确定要拒绝提审吗？'
            });
        });
    </script>
@endpush