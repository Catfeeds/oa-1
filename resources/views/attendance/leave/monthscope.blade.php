<div class="form-group">
    <select class="js-select2-single form-control" name="scope[user_id]" >
        <option value="">全部员工</option>
        @foreach( \App\User::getUsernameAliasList() as $k => $v)
            <option value="{{ $k }}" @if($k == ($a = $scope->userId ?? old("scope[user_id]")) && $a !== NULL) selected="selected" @endif>{{ $v }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <select class="js-select2-single form-control" name="scope[status]" >
        <option value="">确认状态</option>
        @foreach( \App\Models\Attendance\ConfirmAttendance::$stateAdmin as $k => $v)
            <option value="{{ $k }}" @if($k == ($a = $scope->status ?? old("scope[status]")) && $a !== NULL) selected="selected" @endif>{{ $v }}</option>
        @endforeach
    </select>
</div>


<div class="form-group">
    <select class="js-select2-single form-control" name="scope[dept]" id="dept" style="width: 120px">
        <option value="">部门</option>
        @foreach(\App\Models\Sys\Dept::getDeptList() as $k => $dept)
            <option value="{{ $k }}" @if($k == $scope->dept ?? old("scope[dept]")) selected="selected" @endif>{{ $dept }}</option>
        @endforeach
    </select>
</div>