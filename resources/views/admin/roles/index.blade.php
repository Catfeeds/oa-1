@extends('layouts.top-nav')

@section('title', $title)
@section('body-class', 'top-navigation')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $title }}</h5>

                            @if(Entrust::can(['role-all', 'role.create']))
                            <div class="ibox-tools">
                                <a class="btn btn-xs btn-primary" href="{{ route('role.create') }}">
                                    {{ trans('app.添加', ['value' => trans('app.职务')]) }}
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
                                        <th>{{ trans('app.职务') }}</th>
                                        <th>{{ trans('app.名称') }}</th>
                                        <th>{{ trans('app.备注') }}</th>
                                        <th>{{ trans('app.操作') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($data as $v)
                                    <tr>
                                        <td>{{ $v['name'] }}</td>
                                        <td>{{ $v['display_name'] }}</td>
                                        <td>{{ $v['description'] }}</td>
                                        <td>{!!
                                        BaseHtml::tooltip(trans('app.设置'), route('role.edit', ['id' => $v['id']]))
                                        .
                                        BaseHtml::tooltip('权限', route('role.appoint', ['id' => $v['id']]), 'paper-plane')
                                        !!}

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
