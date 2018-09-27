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

                    <input type="submit" class="btn btn-primary btn-submit" value="{{ trans('app.提交') }}">

                    {!! Form::close() !!}
                </div>
            </div>
        </div>

    </div>
</div>