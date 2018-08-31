@extends('crm.side-nav')

@section('title', $title)

@section('content')

    @include('flash::message')

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">
                    {!! Form::open(['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}

                    <div class="form-group @if (!empty($errors->first('billing_cycle'))) has-error @endif">
                        {!! Form::label('billing_cycle', trans('crm.对账周期'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            @include('widget.select-single', ['name' => 'billing_cycle', 'lists' => $billing, 'selected' => $data->billing_cycle ?? old('billing_cycle')])
                            <span class="help-block m-b-none">{{ $errors->first('billing_cycle') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    {{--<div class="form-group @if (!empty($errors->first('currency'))) has-error @endif">
                        {!! Form::label('currency', trans('crm.货币'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            @include('widget.select-single', ['name' => 'currency', 'lists' => $currency, 'selected' => $data->currency ?? old('currency')])
                            <span class="help-block m-b-none">{{ $errors->first('currency') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>--}}

                    <div class="form-group @if (!empty($errors->first('exchange_rate'))) has-error @endif">
                        {!! Form::label('exchange_rate', trans('crm.汇率'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::text('exchange_rate', $data->exchange_rate ?? old('exchange_rate'), [
                                'class' => 'form-control',
                                'placeholder' => trans('app.请输入', ['value' => trans('crm.汇率')]),
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('exchange_rate') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationDifferenceType']))
                                <a href="{{ route('reconciliationDifferenceType') }}"
                                   class="btn btn-info">{{ trans('crm.返回列表') }}</a>
                            @endif
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection

