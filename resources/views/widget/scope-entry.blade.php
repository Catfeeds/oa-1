@include('widget.daterangepicker')

<div class="row m-b-md">
    <div class="col-xs-10">

        <div class="row" id="scope-area">
            <div class="col-md-12">
                <div class="form-inline">
                    {!! Form::open(['class' => 'form', 'id' => 'scope-form', 'method' => 'get', 'url' => Route::getCurrentRequest()->path()]) !!}

                    @if(isset($scope->block))
                        @include($scope->block)
                    @endif

                    @if($scope->displayDates)
                        <div class="form-group">
                            <div class="input-group date col-xs-5">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" id="date-range" class="form-control" style="width: 230px;">
                            </div>
                        </div>
                    @endif

                    <input type="submit" class="btn btn-primary btn-submit" value="{{ trans('app.提交') }}">

                    {!! Form::hidden('scope[startDate]', $scope->startDate, ['id' => 'scope-start-date']) !!}
                    {!! Form::hidden('scope[endDate]', $scope->endDate, ['id' => 'scope-end-date']) !!}

                    {!! Form::close() !!}
                </div>
            </div>
        </div>

    </div>
</div>