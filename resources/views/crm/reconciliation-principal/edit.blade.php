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

                    {!! Form::hidden('product_id', $data['product_id'] ?? old('product_id'), ['id' => 'product_id']) !!}

                    <div class="form-group @if (!empty($errors->first('ops'))) has-error @endif">
                        {!! Form::label('ops', trans('crm.运营专员'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            @include('widget.select-single', ['name' => 'ops', 'lists' => $user, 'selected' => $data['ops'] ?? old('ops')])
                            <span class="help-block m-b-none">{{ $errors->first('ops') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('opd'))) has-error @endif">
                        {!! Form::label('opd', trans('crm.运营主管'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            @include('widget.select-single', ['name' => 'opd', 'lists' => $user, 'selected' => $data['opd'] ?? old('opd')])
                            <span class="help-block m-b-none">{{ $errors->first('opd') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('fac'))) has-error @endif">
                        {!! Form::label('fac', trans('crm.财务计提专员'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            @include('widget.select-single', ['name' => 'fac', 'lists' => $user, 'selected' => $data['fac']?? old('fac')])
                            <span class="help-block m-b-none">{{ $errors->first('fac') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('treasurer'))) has-error @endif">
                        {!! Form::label('treasurer', trans('crm.财务计提主管'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            @include('widget.select-single', ['name' => 'treasurer', 'lists' => $user, 'selected' => $data['treasurer']?? old('treasurer')])
                            <span class="help-block m-b-none">{{ $errors->first('treasurer') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('frc'))) has-error @endif">
                        {!! Form::label('frc', trans('crm.财务对账专员'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            @include('widget.select-single', ['name' => 'frc', 'lists' => $user, 'selected' => $data['frc'] ?? old('frc')])
                            <span class="help-block m-b-none">{{ $errors->first('frc') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('fsr'))) has-error @endif">
                        {!! Form::label('fsr', trans('crm.财务对账主管'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            @include('widget.select-single', ['name' => 'fsr', 'lists' => $user, 'selected' => $data['fsr']?? old('fsr')])
                            <span class="help-block m-b-none">{{ $errors->first('fsr') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationPrincipal']))
                                <a href="{{ route('reconciliationPrincipal') }}"
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
@include('widget.select2')

