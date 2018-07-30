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

                            @if(Entrust::can(['version-all', 'version.create']))
                            <div class="ibox-tools">
                                <a class="btn btn-xs btn-primary" href="{{ route('version.create') }}">
                                    {{ trans('app.添加', ['value' => trans('app.版本')]) }}
                                </a>
                            </div>
                            @endif

                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">

                                @include('flash::message')

                                <table class="table table-hover table-striped tooltip-demo">
                                    <thead>
                                    <tr>
                                        <th>{{ trans('app.版本') }}</th>
                                        <th>{{ trans('app.内容') }}</th>
                                        <th>{{ trans('app.操作') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($data as $v)
                                    <tr>
                                        <td><h4>{{ $v['title'] }}</h4></td>
                                        <td style="text-align:left">{!! $v['content'] !!}</td>
                                        <td>
                                             @if(Entrust::can(['version-all', 'version.edit']))
                                            {!! BaseHtml::tooltip(trans('app.设置'), route('version.edit', ['id' => $v['id']])) !!}
                                             @else
                                             --
                                             @endif
                                        </td>
                                    </tr>
                                    @endforeach

                                    </tbody>
                                </table>

                                {{ $data->links() }}

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
