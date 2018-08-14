@extends('crm.side-nav')

@section('title', $title)

@section('page-head')

    @parent
@endsection

@section('content')

    @include('widget.scope-month')
    @include('flash::message')

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
                <div class="ibox-content tooltip-demo">
                    <div class="row">
                    </div>
                    <div class="stat-view">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    @foreach($header as $v)
                                        <th>{{ $v }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @if($source == \App\Models\Crm\Reconciliation::OPERATION)
                                    @include('widget.audit-button',['first' => 1, 'second' => 2])
                                    @foreach($data as $v)
                                        <tr>
                                            <td>{{ $v['billing_cycle_start'] }}</td>
                                            <td>{{ $v['billing_cycle_end'] }}</td>
                                            <td>{{ $v['income_type'] }}</td>
                                            <td>{{ $v['company'] }}</td>
                                            <td>{{ $v['client'] }}</td>
                                            <td>{{ $v['game_name'] }}</td>
                                            <td>{{ $v['online_name'] }}</td>
                                            <td>{{ $v['business_line'] }}</td>
                                            <td>{{ $v['area'] }}</td>
                                            <td>{{ $v['reconciliation_currency'] }}</td>
                                            <td>{{ $v['os'] }}</td>
                                            <td>{{ $v['divided_type'] }}</td>
                                            <td>{{ $v['backstage_channel'] }}</td>
                                            <td>{{ $v['unified_channel'] }}</td>
                                            <td>{{ $v['backstage_water_other'] }}</td>
                                            <td>{{ $v['backstage_water_rmb'] }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['channel_rate']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['first_division']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['second_division']) }}</td>
                                            <td>{{ $v['second_division_condition'] }}</td>
                                            <td>{{ $v['operation_adjustment'] }}</td>
                                            <td>{{ $v['operation_type'] }}</td>
                                            <td>{{ $v['operation_remark'] }}</td>
                                            <td>{{ $v['operation_user_name'] }}</td>
                                            <td>{{ $v['operation_time'] }}</td>
                                            <td>{{ $v['operation_water_other'] }}</td>
                                            <td>{{ $v['operation_water_rmb'] }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['backstage_water_other']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['backstage_water_other']) }}</td>
                                            <td>
                                                @if($v['review_type'] == 1 && Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit.edit']))
                                                    {!! BaseHtml::tooltip(trans('crm.编辑'), route('reconciliationAudit.edit', ['id' => $v['id'], 'source' => $source]), 'cog fa-lg') !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @elseif($source == \App\Models\Crm\Reconciliation::ACCRUAL)
                                    @include('widget.audit-button',['first' => 3, 'second' => 4])
                                    @foreach($data as $v)
                                        <tr>
                                            <td>{{ $v['billing_cycle_start'] }}</td>
                                            <td>{{ $v['billing_cycle_end'] }}</td>
                                            <td>{{ $v['income_type'] }}</td>
                                            <td>{{ $v['company'] }}</td>
                                            <td>{{ $v['client'] }}</td>
                                            <td>{{ $v['game_name'] }}</td>
                                            <td>{{ $v['online_name'] }}</td>
                                            <td>{{ $v['business_line'] }}</td>
                                            <td>{{ $v['area'] }}</td>
                                            <td>{{ $v['reconciliation_currency'] }}</td>
                                            <td>{{ $v['os'] }}</td>
                                            <td>{{ $v['divided_type'] }}</td>
                                            <td>{{ $v['backstage_channel'] }}</td>
                                            <td>{{ $v['unified_channel'] }}</td>
                                            <td>{{ $v['operation_water_other'] }}</td>
                                            <td>{{ $v['operation_water_rmb'] }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['channel_rate']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['first_division']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['second_division']) }}</td>
                                            <td>{{ $v['second_division_condition'] }}</td>
                                            <td>{{ $v['accrual_adjustment'] }}</td>
                                            <td>{{ $v['accrual_type'] }}</td>
                                            <td>{{ $v['accrual_remark'] }}</td>
                                            <td>{{ $v['accrual_user_name'] }}</td>
                                            <td>{{ $v['accrual_time'] }}</td>
                                            <td>{{ $v['accrual_water_other'] }}</td>
                                            <td>{{ $v['accrual_water_rmb'] }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['operation_water_other']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['operation_water_rmb']) }}</td>
                                            <td>
                                                @if($v['review_type'] == 3 && Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit.edit']))
                                                    {!! BaseHtml::tooltip(trans('crm.编辑'), route('reconciliationAudit.edit', ['id' => $v['id'], 'source' => $source]), 'cog fa-lg') !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @elseif($source == \App\Models\Crm\Reconciliation::RECONCILIATION)
                                    @include('widget.audit-button',['first' => 5, 'second' => 6])
                                    @foreach($data as $v)
                                        <tr>
                                            <td>{{ $v['billing_cycle_start'] }}</td>
                                            <td>{{ $v['billing_cycle_end'] }}</td>
                                            <td>{{ $v['income_type'] }}</td>
                                            <td>{{ $v['company'] }}</td>
                                            <td>{{ $v['client'] }}</td>
                                            <td>{{ $v['game_name'] }}</td>
                                            <td>{{ $v['online_name'] }}</td>
                                            <td>{{ $v['business_line'] }}</td>
                                            <td>{{ $v['area'] }}</td>
                                            <td>{{ $v['reconciliation_currency'] }}</td>
                                            <td>{{ $v['os'] }}</td>
                                            <td>{{ $v['divided_type'] }}</td>
                                            <td>{{ $v['backstage_channel'] }}</td>
                                            <td>{{ $v['unified_channel'] }}</td>
                                            <td>{{ $v['accrual_water_other'] }}</td>
                                            <td>{{ $v['accrual_water_rmb'] }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['channel_rate']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['first_division']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['second_division']) }}</td>
                                            <td>{{ $v['second_division_condition'] }}</td>
                                            <td>{{ $v['reconciliation_adjustment'] }}</td>
                                            <td>{{ $v['reconciliation_type'] }}</td>
                                            <td>{{ $v['reconciliation_remark'] }}</td>
                                            <td>{{ $v['reconciliation_user_name'] }}</td>
                                            <td>{{ $v['reconciliation_time'] }}</td>
                                            <td>{{ $v['reconciliation_water_other'] }}</td>
                                            <td>{{ $v['reconciliation_water_rmb'] }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['accrual_water_other']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['accrual_water_rmb']) }}</td>
                                            <td>
                                                @if($v['review_type'] == 5 && Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit.edit']))
                                                    {!! BaseHtml::tooltip(trans('crm.编辑'), route('reconciliationAudit.edit', ['id' => $v['id'], 'source' => $source]), 'cog fa-lg') !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <div class="col-sm-1 m-b-xs" style="width: 75px;">
                                        {{--"--}}
                                        <a href="{!! route('reconciliationAudit.download', array_merge(Request::all(), ['pid' => $pid, 'source' => $source])) !!}" target="_blank">
                                            <button class="btn btn-success btn-sm"
                                                    data-toggle="tooltip"
                                                    title="导出"
                                                    data-original-title="导出"> 导出
                                            </button>
                                        </a>
                                    </div>
                                    @foreach($data as $v)
                                        <tr>
                                            <td>{{ $v['billing_cycle_start'] }}</td>
                                            <td>{{ $v['billing_cycle_end'] }}</td>
                                            <td>{{ $v['income_type'] }}</td>
                                            <td>{{ $v['company'] }}</td>
                                            <td>{{ $v['client'] }}</td>
                                            <td>{{ $v['game_name'] }}</td>
                                            <td>{{ $v['online_name'] }}</td>
                                            <td>{{ $v['business_line'] }}</td>
                                            <td>{{ $v['area'] }}</td>
                                            <td>{{ $v['reconciliation_currency'] }}</td>
                                            <td>{{ $v['os'] }}</td>
                                            <td>{{ $v['divided_type'] }}</td>
                                            <td>{{ $v['backstage_channel'] }}</td>
                                            <td>{{ $v['unified_channel'] }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['channel_rate']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['first_division']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['second_division']) }}</td>
                                            <td>{{ $v['second_division_condition'] }}</td>

                                            <td>{{ $v['backstage_water_other'] }}</td>
                                            <td>{{ $v['backstage_water_rmb'] }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['backstage_water_other']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['backstage_water_rmb']) }}</td>

                                            <td>{{ $v['operation_adjustment'] }}</td>
                                            <td>{{ $v['operation_type'] }}</td>
                                            <td>{{ $v['operation_remark'] }}</td>
                                            <td>{{ $v['operation_user_name'] }}</td>
                                            <td>{{ $v['operation_time'] }}</td>
                                            <td>{{ $v['operation_water_other'] }}</td>
                                            <td>{{ $v['operation_water_rmb'] }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['operation_water_other']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['operation_water_rmb']) }}</td>


                                            <td>{{ $v['accrual_adjustment'] }}</td>
                                            <td>{{ $v['accrual_type'] }}</td>
                                            <td>{{ $v['accrual_remark'] }}</td>
                                            <td>{{ $v['accrual_user_name'] }}</td>
                                            <td>{{ $v['accrual_time'] }}</td>
                                            <td>{{ $v['accrual_water_other'] }}</td>
                                            <td>{{ $v['accrual_water_rmb'] }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['accrual_water_other']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['accrual_water_rmb']) }}</td>

                                            <td>{{ $v['reconciliation_adjustment'] }}</td>
                                            <td>{{ $v['reconciliation_type'] }}</td>
                                            <td>{{ $v['reconciliation_remark'] }}</td>
                                            <td>{{ $v['reconciliation_user_name'] }}</td>
                                            <td>{{ $v['reconciliation_time'] }}</td>
                                            <td>{{ $v['reconciliation_water_other'] }}</td>
                                            <td>{{ $v['reconciliation_water_rmb'] }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['reconciliation_water_other']) }}</td>
                                            <td>{{ \App\Http\Components\Helpers\CrmHelper::dividedInto($v['channel_rate'],$v['first_division'],$v['second_division'],$v['second_division_condition'],$v['reconciliation_water_rmb']) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@include('widget.bootbox')
@push('scripts')
    <script>
        $('#button').click(function () {
            $("#button").attr('disabled',true);
        });
        $('#warning_button').click(function () {
            $("#warning_button").attr('disabled',true);
        });
    </script>
@endpush