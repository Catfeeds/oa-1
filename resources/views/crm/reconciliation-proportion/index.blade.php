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
                <div class="ibox-content tooltip-demo">
                    <div class="stat-view">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>{{ trans('crm.游戏名') }}</th>
                                    <th>{{ trans('crm.结算周期') }}</th>
                                    <th>{{ trans('crm.客户') }}</th>
                                    <th>{{ trans('crm.后台渠道') }}</th>
                                    <th>{{ trans('crm.渠道费率') }}</th>
                                    <th>{{ trans('crm.一级分成') }}</th>
                                    <th>{{ trans('crm.一级分成备注') }}</th>
                                    <th>{{ trans('crm.二级分成') }}</th>
                                    <th>{{ trans('crm.二级分成备注') }}</th>
                                    <th>{{ trans('crm.二级分成条件') }}</th>
                                    <th>{{ trans('crm.操作人') }}</th>
                                    <th>{{ trans('crm.操作') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $v)
                                    <tr>
                                        <td>{{ $product[$v['product_id']] ?? '未知游戏'.$v['product_id'] }}</td>
                                        <td>{{ $v['billing_cycle'] }}</td>
                                        <td>{{ $v['client'] }}</td>
                                        <td>{{ $v['backstage_channel'] }}</td>
                                        <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['channel_rate']) }}</td>
                                        <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['first_division']) }}</td>
                                        <td>{{ $v['first_division_remark'] }}</td>
                                        <td>{{ \App\Http\Components\Helpers\CrmHelper::percentage($v['second_division']) }}</td>
                                        <td>{{ $v['second_division_remark'] }}</td>
                                        <td>{{ $v['second_division_condition'] }}</td>
                                        <td>{{ $v['user_name'] }}</td>
                                        <td>
                                            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationProportion.edit']))
                                                {!! BaseHtml::tooltip(trans('crm.编辑'), route('reconciliationProportion.edit', ['id' => $v['id'], 'pid' => $pid]), 'cog fa-lg') !!}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $data->appends(Request::all())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection