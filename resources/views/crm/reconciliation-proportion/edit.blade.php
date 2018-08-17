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

                    <div class="form-group @if (!empty($errors->first('channel_rate'))) has-error @endif">
                        {!! Form::label('channel_rate', trans('crm.渠道费率'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            <div class="input-daterange input-group">
                                {!! Form::number('channel_rate', $data->channel_rate*100 ?? old('channel_rate'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('crm.渠道费率')]),
                                ]) !!}
                                <span class="input-group-addon">%</span>
                                <span class="help-block m-b-none">{{ $errors->first('channel_rate') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('first_division'))) has-error @endif">
                        {!! Form::label('first_division', trans('crm.一级分成'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            <div class="input-daterange input-group">
                                {!! Form::number('first_division', $data->first_division*100 ?? old('first_division'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('crm.一级分成')]),
                                ]) !!}
                                <span class="input-group-addon">%</span>
                                <span class="help-block m-b-none">{{ $errors->first('first_division') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('first_division_remark'))) has-error @endif">
                        {!! Form::label('first_division_remark', trans('crm.一级分成备注'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::text('first_division_remark', $data->first_division_remark ?? old('first_division_remark'), [
                                'class' => 'form-control',
                                'placeholder' => trans('app.请输入', ['value' => trans('crm.一级分成备注')]),
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('first_division_remark') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('second_division'))) has-error @endif">
                        {!! Form::label('second_division', trans('crm.二级分成'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            <div class="input-daterange input-group">
                                {!! Form::number('second_division', $data->second_division*100 ?? old('second_division'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('crm.二级分成')]),
                                ]) !!}
                                <span class="input-group-addon">%</span>
                                <span class="help-block m-b-none">{{ $errors->first('second_division') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('second_division_remark'))) has-error @endif">
                        {!! Form::label('second_division_remark', trans('crm.二级分成备注'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">

                            {!! Form::text('second_division_remark', $data->second_division_remark ?? old('second_division_remark'), [
                                'class' => 'form-control',
                                'placeholder' => trans('app.请输入', ['value' => trans('crm.二级分成备注')]),
                            ]) !!}

                            <span class="help-block m-b-none">{{ $errors->first('second_division_remark') }}</span>

                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('second_division_condition'))) has-error @endif">
                        {!! Form::label('second_division_condition', trans('crm.二级分成条件'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::number('second_division_condition', $data->second_division_condition ?? old('second_division_condition'), [
                                'class' => 'form-control',
                                'placeholder' => trans('app.请输入', ['value' => trans('crm.二级分成条件')]),
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('second_division_condition') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationProportion']))
                                <a href="{{ route('reconciliationProportion') }}"
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

