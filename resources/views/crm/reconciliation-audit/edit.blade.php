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

                    <div class="form-group @if (!empty($errors->first('choose'))) has-error @endif">
                        {!! Form::label('choose', trans('crm.调整类型'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            @include('widget.select-single', ['name' => 'choose', 'lists' => \App\Models\Crm\Difference::getList(), 'selected' => ''])
                            <span class="help-block m-b-none">{{ $errors->first('choose') }}</span>
                        </div>
                        <div class="col-sm-3">
                            <button class="btn btn-info btn-rounded btn-xs" id="add" type="button">添加</button>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('remark'))) has-error @endif">
                        {!! Form::label('adjustment', trans('crm.调整'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-9" id="show">
                            @if($data['adjustment'])
                                @foreach(json_decode($data['adjustment'], true) as $k => $v)
                                    <div id="{{ json_decode($data['type'], true)[$k] }}"><span>{{ \App\Models\Crm\Difference::getList()[json_decode($data['type'], true)[$k]] }}：</span><input name="adjustment[]" value="{{ $v }}"><input hidden name="type[]" value="{{ json_decode($data['type'], true)[$k] }}">　<button class="btn btn-danger btn-rounded btn-xs" onclick="del({{ json_decode($data['type'], true)[$k] }})" type="button">删除</button></div>
                                @endforeach
                            @endif
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
@include('widget.bootbox')
@include('widget.select2')
@push('scripts')
    <script>
        $('#add').click(function () {
            var type = {!! json_encode(\App\Models\Crm\Difference::getList()) !!};
            var text = type[$('#choose').val()];
            var val = $('#choose').val();
            if ($('#' + val).length > 0) {
                bootbox.alert('已经添加该调整，请勿重复添加');
            } else {
                $('#show').append('<div id="' + val + '"><span>' + text + '：</span><input name="adjustment[]"><input hidden name="type[]" value="' + val + '">　<button class="btn btn-danger btn-rounded btn-xs" onclick="del(' + val + ')" type="button">删除</button></div>');
            }
        });

        function del(val) {
            $('#' + val).remove();
        }
    </script>
@endpush