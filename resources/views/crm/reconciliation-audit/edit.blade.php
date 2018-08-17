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

                    <div class="form-group @if (!empty($errors->first('adjustment'))) has-error @endif">
                        {!! Form::label('adjustment', trans('crm.调整'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::number('adjustment', $data['adjustment'] ?? old('adjustment'), [
                                'class' => 'form-control',
                                'placeholder' => trans('app.请输入', ['value' => trans('crm.调整')]),
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('adjustment') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('type'))) has-error @endif">
                        {!! Form::label('type', trans('crm.调整类型'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            @include('widget.select-single', ['name' => 'type', 'lists' => $type, 'selected' => $data['type']])
                            <span class="help-block m-b-none">{{ $errors->first('type') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('remark'))) has-error @endif">
                        {!! Form::label('remark', trans('crm.备注'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::textarea('remark', old('remark') ?? $data['remark'], [
                                'rows' => 5,
                                'class' => 'form-control',
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('remark') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                            @if(Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit']))
                                <a href="{{ route('reconciliationAudit') }}"
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