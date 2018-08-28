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
                    <div class="alert alert-info alert-warning">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        批量添加物品使用说明：<br>
                        1.使用Excel表格文件导入（.xlsx）。<br>
                    </div>
                    {!! Form::open(['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}

                    {!! Form::hidden('pid', $pid, ['id' => 'pid']) !!}
                    <div class="form-group @if (!empty($errors->first('excel'))) has-error @endif">
                        {!! Form::label('excel', trans('crm.批量添加'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::file('excel',['class'=>'filestyle', 'data-icon'=>"false"]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('excel') }}</span>
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



