@extends('layouts.top-nav')

@section('title', $title)
@section('body-class', 'top-navigation')

@section('top-nav')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $title }}</h5>

                            @if(Entrust::can(['version-all', 'version']))
                            <div class="ibox-tools">
                                <a class="btn btn-xs btn-primary" href="{{ route('version') }}">
                                    {{ trans('app.列表', ['value' => trans('app.版本')]) }}
                                </a>
                            </div>
                            @endif

                        </div>
                        <div class="ibox-content">

                            {!! Form::open(['class' => 'form-horizontal']) !!}

                            <div class="form-group @if (!empty($errors->first('title'))) has-error @endif">
                                {!! Form::label('name', trans('app.版本'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('title', isset($ver->title) ? $ver->title : old('name'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('app.版本')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('title') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('content'))) has-error @endif">
                                {!! Form::label('content', trans('app.内容'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-9" style="height: 600px;">
                                    {!! Form::textarea('content', isset($ver->content) ? $ver->content : old('content'), [
                                    'class' => 'form-control summernote',
                                    'placeholder' => trans('app.请输入', ['value' => trans('app.内容')]),
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('content') }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                                </div>
                            </div>

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@include('widget.texteditor')

@push('scripts')
<script>
    $(function () {

        $(document).ready(function () {

            $('.summernote').summernote({
                height: 500,
                lang: 'zh-CN'
            });

        });

    });
</script>
@endpush