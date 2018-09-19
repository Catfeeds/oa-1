<div class="form-group">
    <div class="col-xs-2">
        <input type="text" name="scope[name]" value="{{ $scope->name }}" class="form-control col-xs-4"
               placeholder="姓名">
    </div>
</div>
<div class="form-group">
    <div class="col-xs-2">
        <select class="js-select2-single form-control" name="scope[status]" >
            <option value="">申请状态</option>
            @foreach( \App\Models\StaffManage\Entry::$status as $k => $v)
                <option value="{{ $k }}" @if($k == ($a = $scope->statusId ?? old("scope[status]")) && $a !== NULL) selected="selected" @endif>{{ $v }}</option>
            @endforeach
        </select>
    </div>
</div>
