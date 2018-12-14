<div class="form-group">

    <select class="js-select2-single form-control"  onchange="showHoliday()" id="scope_apply_type_id" name="scope[apply_type_id]" >
        <option value="">申请类型</option>
        @foreach(\App\Models\Sys\HolidayConfig::$applyType as $k => $v)
            <option value="{{ $k }}" @if($k == ($scope->applyTypeId ?? old("scope[apply_type_id]"))) selected="selected" @endif>{{ $v }}</option>
        @endforeach
    </select>

</div>

<div class="form-group">
    <select class="js-select2-single form-control" style="width: 15em" id="scope_holiday_id" name="scope[holiday_id]" >
        <option value="">明细类型</option>
{{--        @foreach(\App\Models\Sys\HolidayConfig::getHolidayList() as $k => $v)
            <option value="{{ $k }}" @if($k == ($scope->holidayId ?? old("scope[holiday_id]"))) selected="selected" @endif>{{ $v }}</option>
        @endforeach--}}
    </select>
</div>

<div class="form-group">
    <select class="js-select2-single form-control" name="scope[status]" >
        <option value="">申请状态</option>
        @foreach(\App\Models\Attendance\Leave::$status as $k => $v)
            <option value="{{ $k }}" @if($k == ($a = $scope->statusId ?? old("scope[status]")) && $a !== NULL) selected="selected" @endif>{{ $v }}</option>
        @endforeach
    </select>
</div>
