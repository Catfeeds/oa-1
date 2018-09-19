<div class="form-group">
    <select class="js-select2-single form-control" name="scope[user_id]" >
        <option value="">全部员工</option>
        @foreach( \App\User::getUsernameAliasList() as $k => $v)
            <option value="{{ $k }}" @if($k == ($a = $scope->userId ?? old("scope[user_id]")) && $a !== NULL) selected="selected" @endif>{{ $v }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <select class="js-select2-single form-control" name="scope[dept_id]" >
        <option value="">全部部门</option>
        @foreach( \App\Models\Sys\Dept::getDeptList() as $k => $v)
            <option value="{{ $k }}" @if($k == ($a = $scope->deptId ?? old("scope[dept_id]")) && $a !== NULL) selected="selected" @endif>{{ $v }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <select class="js-select2-single form-control" name="scope[sex]" >
        <option value="">性别</option>
        @foreach( \App\Models\UserExt::$sex as $k => $v)
            <option value="{{ $k }}" @if($k == ($a = $scope->sex ?? old("scope[sex]")) && $a !== NULL) selected="selected" @endif>{{ $v }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <select class="js-select2-single form-control" name="scope[status]" >
        <option value="">员工状态</option>
        @foreach( \App\User::$statusList as $k => $v)
            <option value="{{ $k }}" @if($k == ($a = $scope->statusId ?? old("scope[status]")) && $a !== NULL) selected="selected" @endif>{{ $v }}</option>
        @endforeach
    </select>
</div>
