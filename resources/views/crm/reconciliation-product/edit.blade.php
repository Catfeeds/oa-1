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

                    <div class="form-group @if (!empty($errors->first('product_id'))) has-error @endif">
                        {!! Form::label('product_id', trans('crm.游戏ID'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::number('product_id', $data->product_id ?? old('product_id'), [
                                'class' => 'form-control',
                                'placeholder' => trans('app.请输入', ['value' => trans('crm.游戏ID')]),
                                isset($data->name) ? 'readonly' : '',
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('product_id') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('name'))) has-error @endif">
                        {!! Form::label('name', trans('crm.游戏名'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::text('name', $data->name ?? old('name'), [
                                'class' => 'form-control',
                                'placeholder' => trans('app.请输入', ['value' => trans('crm.游戏名')]),
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('name') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationProduct']))
                                <a href="{{ route('reconciliationProduct') }}"
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

