@extends('attendance.side-nav')

@section('title', $title)

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">
                    {!! Form::open(['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}

                    {{--个人信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.个人信息')}}</h2</label>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('username'))) has-error @endif">
                        {!! Form::label('username', trans('app.工号'), ['class' => 'col-sm-4 control-label'])  !!}
                        <div class="col-sm-3">
                            {!! Form::text('username', empty($username) ? $entry->username : $username, [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('app.工号')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('username') }}</span>
                        </div>
                        <div class="row">
                            <i style="color: red">*</i>
                            <i style="margin-left: 2em" class="fa fa-info-circle"></i> {{'系统当前最新工号: '. $maxUsername }}
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('role_id'))) has-error @endif">
                        {!! Form::label('role_id', trans('app.权限'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            <select name="role_id[]" required="required" multiple="multiple" class="js-select2-multiple form-control js-select2-single">
                                @foreach($roleList as $key => $val)
                                    <option value="{{ $key}}"
                                            @if (isset($entry->role_id) && in_array($key, json_decode($entry->role_id, true)) || $key === \App\Models\Role::getDefaultRole()->id) selected @endif>{{ $val}}</option>
                                @endforeach
                            </select>
                            <span class="help-block m-b-none">{{ $errors->first('role_id') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>

                    <div class="form-group @if (!empty($errors->first('name'))) has-error @endif">
                        {!! Form::label('name', trans('app.姓名'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('name', isset($entry->name) ? $entry->name : old('name'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('app.姓名')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('name') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>

                    <div class="form-group @if (!empty($errors->first('sex'))) has-error @endif">
                        {!! Form::label('sex', trans('app.性别'), ['class' => 'col-sm-4 control-label']) !!}

                        <div class="col-sm-3">
                            {!! Form::select('sex', \App\Models\UserExt::$sex, isset($entry->sex) ? $entry->sex: old('sex'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('app.性别')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('sex') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>

                    <div class="form-group @if (!empty($errors->first('name'))) has-error @endif">
                        {!! Form::label('mobile', trans('app.手机号码'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('mobile', isset($entry->mobile) ? $entry->mobile : old('mobile'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('app.手机号码')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('mobile') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>

                    <div class="form-group @if (!empty($errors->first('email'))) has-error @endif">
                        {!! Form::label('email', trans('staff.个人邮箱'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('email', isset($entry->email) ? $entry->email : old('email'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.个人邮箱')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('email') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>

                    {{--岗位信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.岗位信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group  @if (!empty($errors->first('entry_time'))) has-error @endif">
                        {!! Form::label('entry_time', trans('app.入职时间'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon" style="color: red"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('entry_time', !empty($entry->entry_time) ? date('Y-m-d', strtotime($entry->entry_time)) : old('entry_time'), [
                                'class' => 'form-control date',
                                'placeholder' => trans('app.请输入', ['value' => trans('app.入职时间')]),
                                'required' => true,
                                ]) !!}
                            </div>
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('entry_time') }}</span>
                    </div>

                    <div class="form-group @if (!empty($errors->first('nature_id'))) has-error @endif">
                        {!! Form::label('nature_id', trans('staff.工作性质'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('nature_id', \App\Models\StaffManage\Entry::$nature, isset($entry->nature_id) ? $entry->nature_id: old('nature_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.工作性质')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('nature_id') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>

                    <div class="form-group @if (!empty($errors->first('hire_id'))) has-error @endif">
                        {!! Form::label('hire_id ', trans('staff.招聘类型'), ['class' => 'col-sm-4 control-label']) !!}

                        <div class="col-sm-3">
                            {!! Form::select('hire_id', \App\Models\StaffManage\Entry::$hireTYpe, isset($entry->hire_id) ? $entry->hire_id: old('hire_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.招聘类型')]),
                            'required' => true,
                            'id' => 'hire_id'
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('hire_id') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>
                    <div class="form-group @if (!empty($errors->first('firm_id'))) has-error @endif">
                        {!! Form::label('firm_id', trans('staff.所属公司'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('firm_id', $firm, isset($entry->firm_id) ? $entry->firm_id: old('firm_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.所属公司')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('firm_id') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>

                    <div class="form-group @if (!empty($errors->first('dept_id'))) has-error @endif">
                        {!! Form::label('dept_id', trans('app.部门'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('dept_id', $dept, isset($entry->dept_id) ? $entry->dept_id: old('dept_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('app.部门')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('dept_id') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>

                    <div class="form-group @if (!empty($errors->first('job_id'))) has-error @endif">
                        {!! Form::label('job_id', trans('staff.岗位类型'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('job_id', $job, isset($entry->job_id) ? $entry->job_id: old('job_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.岗位类型')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('job_id') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>

                    <div class="form-group @if (!empty($errors->first('job_name'))) has-error @endif">
                        {!! Form::label('job_name', trans('staff.岗位名称'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('job_name', isset($entry->job_name) ? $entry->job_name : old('job_name'), [
                            'class' => 'form-control ',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.岗位名称')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('job_name') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>

                    {{--关系信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.关系信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('leader_id'))) has-error @endif">
                        {!! Form::label('leader_id', trans('staff.直属上级'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('leader_id', $users, isset($entry->leader_id) ? $entry->leader_id: old('leader_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.直属上级')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('leader_id') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>
                    <div class="form-group @if (!empty($errors->first('tutor_id'))) has-error @endif">
                        {!! Form::label('tutor_id', trans('staff.导师'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('tutor_id', $users, isset($entry->tutor_id) ? $entry->tutor_id: old('tutor_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.导师')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('tutor_id') }}</span>
                        </div>
                        <i style="color: red">*</i>
                    </div>

                    <div class="form-group @if (!empty($errors->first('friend_id'))) has-error @endif">
                        {!! Form::label('friend_id', trans('staff.基友'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('friend_id', $users, isset($entry->friend_id) ? $entry->friend_id: old('friend_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('staff.基友选择'),
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('friend_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('place'))) has-error @endif">
                        {!! Form::label('place', trans('staff.工作位置'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('place', isset($entry->place) ? $entry->place : old('place'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.工作位置')]),
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('place') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('copy_user'))) has-error @endif">
                        {!! Form::label('copy_user', trans('staff.抄送人员'), ['class' => 'col-sm-4 control-label']) !!}

                        <div class="col-sm-3">
                            <select multiple="multiple" name="copy_user[]" id="copy_users" class="js-select2-multiple form-control js-select2-single">
                                @foreach($users as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (in_array($key, $userIds ?: old('copy_user') ?? [])) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                            <span class="help-block m-b-none">{{ $errors->first('copy_user') }}</span>
                        </div>
                    </div>

                    @if(!empty($operateLog))
                        {{--操作日志信息--}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.操作日志信息')}}</h2</label>
                        </div>

                        <div class="form-group">
                            {!! Form::label('copy_user', trans('staff.操作信息'), ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                @foreach($operateLog as $lk => $lv)
                                    <span class="help-block m-b-none">
                                        <a class="btn btn-xs btn-primary">{{ $lv->created_at }}</a>
                                        <a class="btn btn-xs btn-rounded">{{ $users[$lv->opt_uid] ?? '错误数据'}}</a>
                                        <a class="btn btn-xs btn-default btn-rounded btn-outline">{{ $lv->opt_name }} </a>
                                        @if(!empty($lv->memo))
                                            <span style="color: #039"> {!! $lv->memo !!}</span>
                                        @endif
                                    </span>
                                @endforeach

                            </div>
                        </div>
                    @endif

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-5">
                            {!! Form::submit($btnDesc, ['class' => 'btn btn-primary']) !!}
                            <a href="{{ route('entry.list') }}"
                               class="btn btn-info">{{ trans('att.返回列表') }}</a>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>

@endsection
@include('widget.bootbox')
@include('widget.icheck')
@include('widget.select2')
@include('widget.datepicker')
@section('scripts-last')
    <script>
        $(function() {
            $('#js-select2-single').select2();
        });
    </script>
@endsection