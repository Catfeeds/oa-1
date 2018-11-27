@extends('layouts.base')
@section('base')
    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-38">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $title ?? trans('staff.入职信息填写') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="panel-body">
                                <div style="height: 20em" class="tab-content">
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


