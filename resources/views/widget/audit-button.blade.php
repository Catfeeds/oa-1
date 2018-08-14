@if($status == $first && in_array($first, $limitPost))
    @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit.submitReview']))
        <div class="col-sm-1 m-b-xs" style="width: 75px;">
            <a href="{!! route('reconciliationAudit.review', array_merge(Request::all(), ['status' => 1, 'pid' => $pid, 'source' => $source])) !!}">
                <button class="btn btn-info btn-sm" id="button"
                        data-toggle="tooltip"
                        title="提交审核"
                        data-original-title="提交审核"> 提交审核
                </button>
            </a>
        </div>
    @endif
    @if($first > 1)
        @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit.refuse']))
            <div class="col-sm-1 m-b-xs" style="width: 75px;">
                <a href="{!! route('reconciliationAudit.review', array_merge(Request::all(), ['status' => 3, 'pid' => $pid, 'source' => $source])) !!}">
                    <button class="btn btn-warning btn-sm" id="warning_button"
                            data-toggle="tooltip"
                            title="拒绝"
                            data-original-title="拒绝"> 拒绝
                    </button>
                </a>
            </div>
        @endif
    @endif
@elseif($status == $second && in_array($second, $limitPost))
    @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit.review']))
        <div class="col-sm-1 m-b-xs" style="width: 55px;">
            <a href="{!! route('reconciliationAudit.review', array_merge(Request::all(), ['status' => 2, 'pid' => $pid, 'source' => $source])) !!}">
                <button class="btn btn-success btn-sm" id="button"
                        data-toggle="tooltip"
                        title="审核"
                        data-original-title="审核"> 审核
                </button>
            </a>
        </div>
    @endif
    @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit.refuse']))
        <div class="col-sm-1 m-b-xs" style="width: 75px;">
            <a href="{!! route('reconciliationAudit.review', array_merge(Request::all(), ['status' => 3, 'pid' => $pid, 'source' => $source])) !!}">
                <button class="btn btn-warning btn-sm" id="warning_button"
                        data-toggle="tooltip"
                        title="拒绝"
                        data-original-title="拒绝"> 拒绝
                </button>
            </a>
        </div>
    @endif
@endif