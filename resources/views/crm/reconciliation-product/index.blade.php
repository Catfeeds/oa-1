@extends('crm.side-nav')

@section('title', $title)

@section('page-head')

    @parent
    <div class="col-sm-8">
        <div class="title-action">
            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationProduct','reconciliation-reconciliationProduct.create']))
                <a href="{{ route('reconciliationProduct.create') }}"
                   class="btn btn-primary btn-sm">{{ trans('app.添加', ['value' => $title]) }}</a>
            @endif
        </div>
    </div>

@endsection

@section('content')
    <div class="alert alert-info alert-warning">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        游戏列表说明：<br>
        游戏ID必须要和研发后天一致
    </div>
    @include('flash::message')

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
                                    <th>{{ trans('crm.游戏ID') }}</th>
                                    <th>{{ trans('crm.游戏名') }}</th>
                                    <th>{{ trans('crm.操作') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $v)
                                    <tr>
                                        <td>{{ $v['product_id'] }}</td>
                                        <td>{{ $v['name'] }}</td>
                                        <td>
                                            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationProduct','reconciliation-reconciliationProduct.edit']))
                                                {!! BaseHtml::tooltip(trans('crm.编辑'), route('reconciliationProduct.edit', ['id' => $v['id']]), 'cog fa-lg') !!}
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