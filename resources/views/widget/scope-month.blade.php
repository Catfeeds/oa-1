@include('widget.datepicker')
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
                            <input class="form-control date_month start-date" id="startDate" type="text"
                                   value="{{ date('Y-m', strtotime($scope->startDate)) }}">
                            {{ trans('app.到') }}
                            <input class="form-control date_month end-date" id="endDate" type="text"
                                   value="{{ date('Y-m', strtotime($scope->endDate)) }}">
                        </div>
                    @endif
                    <input type="button" class="btn btn-primary btn-submit" value="{{ trans('app.提交') }}">


                    @if(isset($source))
                        {!! Form::hidden('source', $source, ['id' => 'source']) !!}
                    @endif
                    {!! Form::hidden('scope[startDate]', $scope->startDate, ['id' => 'scope-start-date']) !!}

                    {!! Form::close() !!}
                </div>
            </div>
        </div>

    </div>
</div>

@section('scripts-last')

    <script>
        $('.start-date').on('changeDate', function (e) {
            $('#scope-start-date').val(e.date.getTime());
        });
        $('.end-date').on('changeDate', function (e) {
            $('#scope-end-date').val(e.date.getTime());
        });
        $('.btn-submit').click(function () {
                    @if(stristr(\Request::path(), "reconciliation-audit"))
            var st = new Date($("#startDate").val().replace(/-/g, '/')).getMonth();
            var et = new Date($("#endDate").val().replace(/-/g, '/')).getMonth();
            if (et == st) {
                $('#scope-form').submit();
            } else {
                bootbox.alert('时间超出限制!');
            }
            @else
            $('#scope-form').submit();
            @endif
        });
    </script>

@endsection