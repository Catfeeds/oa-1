@extends('crm.side-nav')

@section('title', $title)

@section('page-head')

    @parent
    {{--<div class="col-sm-8">
        <div class="title-action">
            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationExchangeRate', 'reconciliation-reconciliationExchangeRate.create']))
                <a href="{{ route('reconciliationExchangeRate.create') }}"
                   class="btn btn-primary btn-sm">{{ trans('app.添加', ['value' => $title]) }}</a>
            @endif
        </div>
    </div>--}}

@endsection

@section('content')
    @include('flash::message')
    <div class="alert alert-info alert-warning">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        汇率管理说明：<br>
        如果当月汇率填写完整后，请点击<button class="btn btn-xs btn-info" id="conversion">转化</button> 转化后台流水,<br>
        且转化完整后，再通知下一步
    </div>
    @include('widget.scope-date', ['scope' => $scope])
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
                                    <th>{{ trans('crm.对账周期') }}</th>
                                    <th>{{ trans('crm.货币') }}</th>
                                    <th>{{ trans('crm.汇率') }}</th>
                                    <th>{{ trans('crm.操作') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $v)
                                    <tr>
                                        <td>{{ $v['billing_cycle'] }}</td>
                                        <td>{{ $currency[$v['currency']] }}</td>
                                        <td>{{ $v['exchange_rate'] }}</td>
                                        <td>
                                            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationExchangeRate', 'reconciliation-reconciliationExchangeRate.edit']))
                                                {!! BaseHtml::tooltip(trans('crm.编辑'), route('reconciliationExchangeRate.edit', ['id' => $v['id']]), 'cog fa-lg') !!}
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
@include('widget.select2')
@include('widget.bootbox')
@section('scripts-last')
    <script>
        $(function () {
            $("#conversion").click(function () {
                $.get("{{ route('reconciliationExchangeRate.conversion', ['billing' => $scope->billing]) }}", function (result) {
                    bootbox.alert(result.message);
                });
            });
        });
    </script>
@endsection