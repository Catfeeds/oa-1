<div class="form-group">
    {!! Form::text('scope[daily_user_id]', '', [ 'class' => 'form-control', 'placeholder' => trans('app.账号') ]) !!}
</div>
<div class="form-group">
    {!! Form::text('scope[daily_alias]', '', [ 'class' => 'form-control', 'placeholder' => trans('app.名称') ]) !!}
</div>
<div class="form-group">
    <select class="js-select2-single form-control" name="scope[daily_dept]" id="dept" style="width: 120px">
        <option value="">部门</option>
        @foreach(\App\Models\Sys\Dept::getDeptList() as $dept_id => $dept)
            <option value="{{ $dept_id }}" {{--@if($k == $scope->deptId ?? old("scope[dept]")) selected="selected" @endif--}}>{{ $dept }}</option>
        @endforeach
    </select>
</div>