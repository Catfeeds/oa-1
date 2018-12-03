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

                    <div class="form-group">
                        {!! Form::label('username', trans('staff.工号'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('username', $entry->username  ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('role_id'))) has-error @endif">
                        {!! Form::label('role_id', trans('app.权限'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            <select name="role_id[]"  multiple="multiple" class="js-select2-multiple form-control js-select2-single">
                                @foreach($roleList as $key => $val)
                                    <option value="{{ $key}}"
                                            @if (isset($entry->role_id) && in_array($key, json_decode($entry->role_id, true) ? : old('role_id') ?? [])) selected @endif>{{ $val}}</option>
                                @endforeach
                            </select>
                            <span class="help-block m-b-none">{{ $errors->first('role_id') }}</span>
                        </div>

                    </div>

                    <div class="form-group @if (!empty($errors->first('name'))) has-error @endif">
                        {!! Form::label('alias', trans('app.姓名'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('name', isset($entry->name) ? $entry->name : old('name'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('app.姓名')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('name') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('sex'))) has-error @endif">
                        {!! Form::label('sex', trans('app.性别'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('sex', \App\Models\UserExt::$sex, isset($entry->sex) ? $entry->sex: old('sex'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('app.性别')]),
                            'required' => true,
                            'id' => 'sex'
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('sex') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('mobile'))) has-error @endif">
                        {!! Form::label('mobile', trans('app.手机号码'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('mobile', isset($entry->mobile) ? $entry->mobile : old('mobile'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('app.手机号码')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('mobile') }}</span>
                        </div>
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
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('entry_time', $entry->entry_time ?? old('entry_time'), [
                                'class' => 'form-control date_time',
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
                    </div>

                    <div class="form-group">
                        {!! Form::label('hire_id', '招聘类型', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('hire_id', \App\Models\StaffManage\Entry::$hireTYpe, isset($entry->hire_id) ? $entry->hire_id: old('hire_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.招聘类型')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('hire_id') }}</span>
                        </div>

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

                    </div>

                    <div class="form-group @if (!empty($errors->first('job_name'))) has-error @endif">
                        {!! Form::label('job_name', trans('staff.岗位名称'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('job_name', isset($entry->job_name) ? $entry->job_name : old('job_name'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.岗位名称')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('job_name') }}</span>
                        </div>
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
                    </div>

                    <div class="form-group @if (!empty($errors->first('friend_id'))) has-error @endif">
                        {!! Form::label('friend_id', trans('staff.基友'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('friend_id', $users, isset($entry->friend_id) ? $entry->friend_id: old('friend_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.基友')]),
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
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('place') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('copy_user'))) has-error @endif">
                        {!! Form::label('copy_user', trans('staff.抄送人员'), ['class' => 'col-sm-4 control-label']) !!}

                        <div class="col-sm-3">
                            <select required="required" multiple="multiple" name="copy_user[]" id="copy_users" class="js-select2-multiple form-control js-select2-single">
                                @foreach($users as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (in_array($key, $userIds ?: old('copy_user') ?? [])) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                            <span class="help-block m-b-none">{{ $errors->first('copy_user') }}</span>
                        </div>
                    </div>

                    {{--基本信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.基本信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('birthday'))) has-error @endif">
                        {!! Form::label('birthday', trans('staff.生日'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('entry[birthday]', !empty($entry->birthday) ? date('Y-m-d', strtotime($entry->birthday)) : '', [
                                'class' => 'form-control date',
                                'required' => true,
                                ]) !!}
                                <span class="help-block m-b-none">{{ $errors->first('birthday') }}</span>
                            </div>
                        </div>
                        @foreach(\App\Models\StaffManage\Entry::$birthdayType as $k => $v)
                            <label class="radio-inline i-checks">
                                <input type="radio" name="entry[birthday_type]" value="{{$k}}" @if($k === (int)($entry->birthday_type ?? \App\Models\StaffManage\Entry::GREGORIAN_CALENDAR)) checked @endif> {{ $v }}
                            </label>
                        @endforeach
                    </div>

                    <div class="form-group @if (!empty($errors->first('ethnic'))) has-error @endif">
                        {!! Form::label('ethnic', trans('staff.民族'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('entry[ethnic_id]', $ethnic, !empty($entry->ethnic_id) ? $entry->ethnic_id : old('ethnic_id'), [
                             'class' => 'form-control js-select2-single',
                             'placeholder' => trans('app.请选择', ['value' => trans('staff.民族')]),
                             'required' => true,
                             ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('ethnic') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('birthplace'))) has-error @endif">
                        {!! Form::label('birthplace', trans('staff.籍贯'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[birthplace]', isset($entry->birthplace) ? $entry->birthplace: old('birthplace'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.籍贯')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('birthplace') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('political'))) has-error @endif">
                        {!! Form::label('political', trans('staff.政治面貌'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('entry[political_id]', \App\Models\UserExt::$political, !empty($entry->political_id) ? $entry->political_id : old('political_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.政治面貌')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('political') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('marital_status'))) has-error @endif">
                        {!! Form::label('marital_status', trans('staff.婚姻状况'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('entry[marital_status]', \App\Models\UserExt::$marital, isset($entry->marital_status) ? $entry->marital_status: old('marital_status'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.婚姻状况')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('marital_status') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('blood_type'))) has-error @endif">
                        {!! Form::label('blood_type', trans('staff.血型'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('entry[blood_type]', \App\Models\UserExt::$blood, isset($entry->blood_type) ? $entry->blood_type: old('blood_type'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.血型')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('blood_type') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('genus_id'))) has-error @endif">
                        {!! Form::label('genus_id', trans('staff.属相'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('entry[genus_id]', \App\Models\UserExt::$genus, isset($entry->genus_id) ? $entry->genus_id: old('genus_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.属相')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('genus_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('constellation_id'))) has-error @endif">
                        {!! Form::label('constellation_id', trans('staff.星座'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('entry[constellation_id]', \App\Models\UserExt::$constellation, isset($entry->constellation_id) ? $entry->constellation_id: old('constellation_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.星座')]),
                            'onkeyup' => "value=value.replace(/^(0+)|[^\d]+/g,'')"
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('constellation_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('height'))) has-error @endif">
                        {!! Form::label('dept', trans('staff.身高'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::number('entry[height]', isset($entry->height) ? $entry->height: old('height'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.身高')]),
                            'step' => 0.1
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('height') }}</span>
                        </div>
                        <div class="col-sm-2">
                            <span class="help-block m-b-none">
                                <i class="fa fa-info-circle"></i> {{ trans('单位：厘米(cm)') }}
                            </span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('weight'))) has-error @endif">
                        {!! Form::label('weight', trans('staff.体重'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::number('entry[weight]', isset($entry->weight) ? $entry->weight: old('weight'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.体重')]),
                            'step' => 0.1
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('weight') }}</span>
                        </div>
                        <div class="col-sm-2">
                            <span class="help-block m-b-none">
                                <i class="fa fa-info-circle"></i> {{ trans('单位：公斤/千克(kg)') }}
                            </span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('entry.salary_card'))) has-error @endif">
                        {!! Form::label('salary_card', trans('app.工资卡'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[salary_card]', !empty($entry->salary_card) ? $entry->salary_card : old('salary_card'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('app.工资卡')]),
                            'data-mask' => '9999 9999 9999 9999'

                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('entry.salary_card') }}</span>
                        </div>
                        <div class="row">
                            <i style="color: red">*</i>
                            <i style="margin-left: 1em" class="fa fa-info-circle"></i> {{ trans('请填写本人招商银行广州储蓄卡账号') }}
                        </div>
                    </div>


                    {{--联系信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.联系信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('entry.used_email'))) has-error @endif">
                        {!! Form::label('used_email', trans('staff.QQ邮箱'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::email('entry[used_email]', !empty($entry->used_email) ? $entry->used_email: old('used_email'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.QQ邮箱')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('entry.used_email') }}</span>
                        </div>
                        <div class="row">
                            <i style="color: red">*</i>
                            <i class="fa fa-info-circle"></i> {{ trans('此QQ邮箱将与工作邮箱绑定') }}
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('qq'))) has-error @endif">
                        {!! Form::label('qq', trans('staff.QQ号码'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[qq]', isset($entry->qq) ? $entry->qq: old('qq'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.QQ号码')]),
                            'onkeyup' => "this.value=this.value.replace(/\D/g,'')"
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('qq') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('live_address'))) has-error @endif">
                        {!! Form::label('live_address', trans('staff.目前住址'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[live_address]', isset($entry->live_address) ? $entry->live_address: old('live_address'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.目前住址')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('live_address') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('family_num'))) has-error @endif">
                        {!! Form::label('family_num', trans('staff.家庭成员'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-7">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 6em;">姓名</th>
                                    <th style="width: 3em;">年龄</th>
                                    <th style="width: 6em;">与本人关系</th>
                                    <th style="width: 10em;">单位/职务</th>
                                    <th style="width: 8em;">电话</th>
                                </tr>
                                </thead>
                                <tbody>
                                @for($i = 0; $i <= \App\Models\StaffManage\Entry::CREATE_FAMILY_NUM; $i++)
                                    <tr>
                                        <td><input name="entry[family_num][{{$i}}][name]" value="{{!empty($familyNum[$i]['name']) ? $familyNum[$i]['name'] : ''}}" style="width: 6em;" type="text"></td>
                                        <td><input name="entry[family_num][{{$i}}][age]" value="{{!empty($familyNum[$i]['age']) ? $familyNum[$i]['age'] : ''}}" style="width: 3em;" type="text"></td>
                                        <td><input name="entry[family_num][{{$i}}][relation]" value="{{!empty($familyNum[$i]['relation']) ? $familyNum[$i]['relation'] : ''}}" style="width: 100%;" type="text"></td>
                                        <td><input name="entry[family_num][{{$i}}][position]" value="{{!empty($familyNum[$i]['position']) ? $familyNum[$i]['position'] : ''}}" style="width: 6em;" type="text"></td>
                                        <td><input maxlength="11" onkeyup="this.value=this.value.replace(/\D/g,'')" name="entry[family_num][{{$i}}][phone]" value="{{!empty($familyNum[$i]['phone']) ? $familyNum[$i]['phone'] : ''}}" style="width: 8em;" type="text"></td>
                                    </tr>
                                @endfor
                                </tbody>
                            </table>
                            <span class="help-block m-b-none">{{ $errors->first('entry.family_num') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('urgent_name'))) has-error @endif">
                        {!! Form::label('urgent_name', trans('staff.紧急联系人姓名'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[urgent_name]', isset($entry->urgent_name) ? $entry->urgent_name: old('urgent_name'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.紧急联系人姓名')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('urgent_name') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('urgent_bind'))) has-error @endif">
                        {!! Form::label('urgent_bind', trans('staff.与紧急联系人的关系'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[urgent_bind]', isset($entry->urgent_bind) ? $entry->urgent_bind: old('urgent_bind'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.与紧急联系人的关系')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('urgent_bind') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('urgent_tel'))) has-error @endif">
                        {!! Form::label('urgent_tel', trans('staff.紧急联系人电话'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[urgent_tel]', isset($entry->urgent_tel) ? $entry->urgent_tel: old('urgent_tel'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.紧急联系人电话')]),
                            'required' => true,
                            'maxlength' => 11,
                            'onkeyup' => "this.value=this.value.replace(/\D/g,'')"
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('urgent_tel') }}</span>
                        </div>
                    </div>

                    {{--学历信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.学历信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('education_id'))) has-error @endif">
                        {!! Form::label('education_id', trans('staff.最高学历'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('entry[education_id]', \App\Models\UserExt::$education, isset($entry->education_id) ? $entry->education_id: old('education_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.最高学历')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('education_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('school_id'))) has-error @endif">
                        {!! Form::label('school_id', trans('staff.毕业学校'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('entry[school_id]', $school, isset($entry->school_id) ? $entry->school_id: old('school_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.毕业学校')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('school_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group  @if (!empty($errors->first('graduation_time'))) has-error @endif">
                        {!! Form::label('graduation_time', trans('staff.毕业时间'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('entry[graduation_time]', $entry->graduation_time ?? old('graduation_time'), [
                                'class' => 'form-control date',
                                'placeholder' => trans('app.请输入', ['value' => trans('staff.毕业时间')]),
                                'required' => true,
                                ]) !!}
                            </div>
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('entry_time') }}</span>
                    </div>

                    <div class="form-group @if (!empty($errors->first('specialty'))) has-error @endif">
                        {!! Form::label('specialty', trans('staff.专业'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[specialty]', isset($entry->specialty) ? $entry->specialty: old('specialty'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.专业')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('specialty') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('degree'))) has-error @endif">
                        {!! Form::label('degree', trans('staff.学位'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[degree]', isset($entry->degree) ? $entry->degree : old('degree'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.学位')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('degree') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>


                    {{--工作信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.工作信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('education_id', trans('staff.工作经历'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-8">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 5em;">起止年月</th>
                                    <th style="width: 2em;">期限</th>
                                    <th style="width: 12em;">在何处工作</th>
                                    <th style="width: 5em;">职务</th>
                                    <th style="width: 3em;">月收入</th>
                                    <th style="width: 12em;">直属上司</th>
                                    <th style="width: 8em;">联系电话</th>
                                </tr>
                                </thead>
                                <tbody>
                                @for($i = 0; $i <= \App\Models\StaffManage\Entry::CREATE_WORK_HISTORY_NUM; $i++)
                                    <tr>
                                        <td><input name="entry[work_history][{{$i}}][time]" value="{{!empty($workHistory[$i]['time']) ? $workHistory[$i]['time'] : ''}}" style="width: 6em;" type="text"></td>
                                        <td><input name="entry[work_history][{{$i}}][deadline]" value="{{!empty($workHistory[$i]['deadline']) ? $workHistory[$i]['deadline'] : ''}}" style="width: 3em;" type="text"></td>
                                        <td><input name="entry[work_history][{{$i}}][work_place]" value="{{!empty($workHistory[$i]['work_place']) ? $workHistory[$i]['work_place'] : ''}}" style="width: 100%;" type="text"></td>
                                        <td><input name="entry[work_history][{{$i}}][position]" value="{{!empty($workHistory[$i]['position']) ? $workHistory[$i]['position'] : ''}}" style="width: 5em;" type="text"></td>
                                        <td><input name="entry[work_history][{{$i}}][income]" value="{{!empty($workHistory[$i]['income']) ? $workHistory[$i]['income'] : ''}}" style="width: 4em;" type="text"></td>
                                        <td><input name="entry[work_history][{{$i}}][boss]" value="{{!empty($workHistory[$i]['boss']) ? $workHistory[$i]['boss'] : ''}}" style="width: 100%;" type="text"></td>
                                        <td><input maxlength="11" onkeyup="this.value=this.value.replace(/\D/g,'')" name="entry[work_history][{{$i}}][phone]"  value="{{!empty($workHistory[$i]['phone']) ? $workHistory[$i]['phone'] : ''}}" style="width: 8em;" type="text"></td>
                                    </tr>
                                @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('project_empiric'))) has-error @endif">
                        {!! Form::label('project_empiric', trans('staff.项目经验'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::textarea('entry[project_empiric]', !empty($entry->project_empiric) ? $entry->project_empiric : old('project_empiric'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.项目经验')]),
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('project_empiric') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('awards'))) has-error @endif">
                        {!! Form::label('awards', trans('staff.获奖情况'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::textarea('entry[awards]', !empty($entry->awards) ? $entry->awards : '', [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.获奖情况')]),
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('awards') }}</span>
                        </div>
                    </div>


                    {{--户籍档案信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.户籍档案信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('card_id'))) has-error @endif">
                        {!! Form::label('card_id', trans('staff.身份证号码'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[card_id]', !empty($entry->card_id) ? $entry->card_id : old('card_id'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.身份证号码')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('card_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('card_address'))) has-error @endif">
                        {!! Form::label('card_address', trans('staff.身份证地址'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[card_address]', !empty($entry->card_address) ? $entry->card_address : old('card_address'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.身份证地址')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('card_address') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('census'))) has-error @endif">
                        {!! Form::label('census', trans('staff.户籍类型'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry[census]', !empty($entry->census) ? $entry->census : old('census'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.户籍类型')]),
                            'required' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('census') }}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('firm_call', trans('app.是否公司挂靠'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            @foreach(\App\Models\UserExt::$firmCall as $k => $v)
                                <label class="radio-inline i-checks">
                                    <input type="radio" name="entry[firm_call]" value="{{$k}}" @if($k === (int)($entry->firm_call ?? 0)) checked @endif> {{ $v }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-5">
                            {!! Form::submit(trans('app.确认编辑'), ['class' => 'btn btn-primary']) !!}
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

        });
    </script>
@endsection
