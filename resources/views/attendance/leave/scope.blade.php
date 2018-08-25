<div class="form-group">
    <select class="js-select2-single form-control" name="scope[holiday_id]" >
        <option value="">假期类型</option>
        @foreach(\App\Models\Sys\HolidayConfig::getHolidayList() as $k => $v)
            <option value="{{ $k }}" @if($k == ($scope->holiday_id ?? old('holiday_id'))) selected="selected" @endif>{{ $v }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <select class="js-select2-single form-control" name="scope[holiday_id]" >
        <option value="">申请状态</option>
        @foreach(\App\Models\Attendance\Leave:: as $k => $v)
            <option value="{{ $k }}" @if($k == ($scope->holiday_id ?? old('holiday_id'))) selected="selected" @endif>{{ $v }}</option>
        @endforeach
    </select>
</div>