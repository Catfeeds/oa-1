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
                            {!! Form::text('username', $user->username  ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('alias'))) has-error @endif">
                        {!! Form::label('alias', trans('app.姓名'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('alias', isset($user->alias) ? $user->alias : old('alias'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('app.姓名')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('alias') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('sex'))) has-error @endif">
                        {!! Form::label('sex', trans('app.性别'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('sex', \App\Models\UserExt::$sex, isset($user->userExt->sex) ? $user->userExt->sex: old('sex'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('app.性别')]),
                            'disabled' => true,
                            'id' => 'sex'
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('sex') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('mobile'))) has-error @endif">
                        {!! Form::label('mobile', trans('app.手机号码'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('mobile', isset($user->mobile) ? $user->mobile : old('mobile'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('app.手机号码')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('mobile') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('email'))) has-error @endif">
                        {!! Form::label('email', trans('staff.个人邮箱'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('email', isset($user->email) ? $user->email : old('email'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.个人邮箱')]),
                            'disabled' => true,
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
                                {!! Form::text('entry_time', $user->userExt->entry_time ?? old('entry_time'), [
                                'class' => 'form-control date_time',
                                'placeholder' => trans('app.请输入', ['value' => trans('app.入职时间')]),
                                'disabled' => true,
                                ]) !!}
                            </div>
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('entry_time') }}</span>
                    </div>

                    <div class="form-group @if (!empty($errors->first('nature_id'))) has-error @endif">
                        {!! Form::label('nature_id', trans('staff.工作性质'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('nature_id', \App\Models\StaffManage\Entry::$nature, isset($user->userExt->nature_id) ? $user->userExt->nature_id: old('nature_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.工作性质')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('nature_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('hire_id', '招聘类型', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('hire_id', \App\Models\StaffManage\Entry::$hireTYpe, isset($user->userExt->hire_id) ? $user->userExt->hire_id: old('hire_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.招聘类型')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('hire_id') }}</span>
                        </div>

                    </div>
                    <div class="form-group @if (!empty($errors->first('firm_id'))) has-error @endif">
                        {!! Form::label('firm_id', trans('staff.所属公司'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('firm_id', $firm, isset($user->userExt->firm_id) ? $user->userExt->firm_id: old('firm_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.所属公司')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('firm_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('dept_id'))) has-error @endif">
                        {!! Form::label('dept_id', trans('app.部门'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('dept_id', $dept, isset($user->dept_id) ? $user->dept_id: old('dept_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('app.部门')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('dept_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('job_id'))) has-error @endif">
                        {!! Form::label('job_id', trans('staff.岗位类型'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('job_id', $job, isset($user->job_id) ? $user->job_id: old('job_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.岗位类型')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('job_id') }}</span>
                        </div>

                    </div>

                    <div class="form-group @if (!empty($errors->first('job_name'))) has-error @endif">
                        {!! Form::label('job_name', trans('staff.岗位名称'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('job_name', isset($user->userExt->job_name) ? $user->userExt->job_name : old('job_name'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.岗位名称')]),
                            'disabled' => true,
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
                            {!! Form::select('leader_id', $users, isset($user->userExt->leader_id) ? $user->userExt->leader_id: old('leader_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.直属上级')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('leader_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('tutor_id'))) has-error @endif">
                        {!! Form::label('tutor_id', trans('staff.导师'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('tutor_id', $users, isset($user->userExt->tutor_id) ? $user->userExt->tutor_id: old('tutor_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.导师')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('tutor_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('friend_id'))) has-error @endif">
                        {!! Form::label('friend_id', trans('staff.基友'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('friend_id', $users, isset($user->userExt->friend_id) ? $user->userExt->friend_id: old('friend_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.基友')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('friend_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('place'))) has-error @endif">
                        {!! Form::label('place', trans('staff.工作位置'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('place', isset($user->userExt->place) ? $user->userExt->place : old('place'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.工作位置')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('place') }}</span>
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
                                {!! Form::text('birthday', !empty($user->userExt->birthday) ? date('Y-m-d', strtotime($user->userExt->birthday)) : '', [
                                'class' => 'form-control date',
                                'disabled' => true,
                                ]) !!}
                                <span class="help-block m-b-none">{{ $errors->first('birthday') }}</span>
                            </div>
                        </div>
                        @foreach(\App\Models\StaffManage\Entry::$birthdayType as $k => $v)
                            <label class="radio-inline i-checks">
                                <input type="radio"  disabled="disabled" name="birthday_type" value="{{$k}}" @if($k === (int)($user->userExt->birthday_type ?? \App\Models\StaffManage\Entry::GREGORIAN_CALENDAR)) checked @endif> {{ $v }}
                            </label>
                        @endforeach
                    </div>

                    <div class="form-group @if (!empty($errors->first('ethnic'))) has-error @endif">
                        {!! Form::label('ethnic', trans('staff.民族'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('ethnic_id', $ethnic, !empty($user->userExt->ethnic_id) ? $user->userExt->ethnic_id : old('ethnic_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.民族')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('ethnic') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('birthplace'))) has-error @endif">
                        {!! Form::label('birthplace', trans('staff.籍贯'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('birthplace', isset($user->userExt->birthplace) ? $user->userExt->birthplace: old('birthplace'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.籍贯')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('birthplace') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('political'))) has-error @endif">
                        {!! Form::label('political', trans('staff.政治面貌'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('political_id', \App\Models\UserExt::$political, !empty($user->userExt->political_id) ? $user->userExt->political_id : old('political_id'), [
                           'class' => 'form-control js-select2-single',
                           'placeholder' => trans('app.请选择', ['value' => trans('staff.政治面貌')]),
                           'disabled' => true,
                           ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('political') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('marital_status'))) has-error @endif">
                        {!! Form::label('marital_status', trans('staff.婚姻状况'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('marital_status', \App\Models\UserExt::$marital, isset($user->userExt->marital_status) ? $user->userExt->marital_status: old('marital_status'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.婚姻状况')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('marital_status') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('blood_type'))) has-error @endif">
                        {!! Form::label('blood_type', trans('staff.血型'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('blood_type', \App\Models\UserExt::$blood, isset($user->userExt->blood_type) ? $user->userExt->blood_type: old('blood_type'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.血型')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('blood_type') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('genus_id'))) has-error @endif">
                        {!! Form::label('genus_id', trans('staff.属相'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('genus_id', \App\Models\UserExt::$genus, isset($user->userExt->genus_id) ? $user->userExt->genus_id: old('genus_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.属相')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('genus_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('constellation_id'))) has-error @endif">
                        {!! Form::label('constellation_id', trans('staff.星座'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('constellation_id', \App\Models\UserExt::$constellation, isset($user->userExt->constellation_id) ? $user->userExt->constellation_id: old('constellation_id'), [
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
                            {!! Form::number('height', isset($user->userExt->height) ? $user->userExt->height: old('height'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.身高')]),
                            'step' => 0.1,
                            'disabled' => true,
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
                            {!! Form::number('weight', isset($user->userExt->weight) ? $user->userExt->weight: old('weight'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.体重')]),
                            'step' => 0.1,
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('weight') }}</span>
                        </div>
                        <div class="col-sm-2">
                            <span class="help-block m-b-none">
                                <i class="fa fa-info-circle"></i> {{ trans('单位：公斤/千克(kg)') }}
                            </span>
                        </div>
                    </div>

                    {{--联系信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.联系信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('qq'))) has-error @endif">
                        {!! Form::label('qq', trans('staff.QQ号码'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('qq', isset($user->userExt->qq) ? $user->userExt->qq: old('qq'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.QQ号码')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('qq') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('live_address'))) has-error @endif">
                        {!! Form::label('live_address', trans('staff.目前住址'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('live_address', isset($user->userExt->live_address) ? $user->userExt->live_address: old('live_address'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.目前住址')]),
                            'disabled' => true,
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
                                    <th>姓名</th>
                                    <th>年龄</th>
                                    <th>与本人关系</th>
                                    <th>单位/职务</th>
                                    <th>电话</th>
                                </tr>
                                </thead>
                                <tbody>
                                @for($i = 0; $i <= \App\Models\StaffManage\Entry::CREATE_FAMILY_NUM; $i++)
                                    <tr>
                                        <td>{{!empty($familyNum[$i]['name']) ? $familyNum[$i]['name'] : ''}}</td>
                                        <td>{{!empty($familyNum[$i]['age']) ? $familyNum[$i]['age'] : ''}}</td>
                                        <td>{{!empty($familyNum[$i]['relation']) ? $familyNum[$i]['relation'] : ''}}</td>
                                        <td>{{!empty($familyNum[$i]['position']) ? $familyNum[$i]['position'] : ''}}</td>
                                        <td>{{!empty($familyNum[$i]['phone']) ? $familyNum[$i]['phone'] : ''}}</td>
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
                            {!! Form::text('urgent_name', isset($user->userExt->urgent_name) ? $user->userExt->urgent_name: old('urgent_name'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.紧急联系人姓名')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('urgent_name') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('urgent_bind'))) has-error @endif">
                        {!! Form::label('urgent_bind', trans('staff.与紧急联系人的关系'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('urgent_bind', isset($user->userExt->urgent_bind) ? $user->userExt->urgent_bind: old('urgent_bind'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.与紧急联系人的关系')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('urgent_bind') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('urgent_tel'))) has-error @endif">
                        {!! Form::label('urgent_tel', trans('staff.紧急联系人电话'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('urgent_tel', isset($user->userExt->urgent_tel) ? $user->userExt->urgent_tel: old('urgent_tel'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.紧急联系人电话')]),
                            'disabled' => true,
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
                            {!! Form::select('education_id', \App\Models\UserExt::$education, isset($user->userExt->education_id) ? $user->userExt->education_id: old('education_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.最高学历')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('education_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('school_id'))) has-error @endif">
                        {!! Form::label('school_id', trans('staff.毕业学校'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('school_id', $school, isset($user->userExt->school_id) ? $user->userExt->school_id: old('school_id'), [
                            'class' => 'form-control js-select2-single',
                            'placeholder' => trans('app.请选择', ['value' => trans('staff.毕业学校')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('school_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group  @if (!empty($errors->first('graduation_time'))) has-error @endif">
                        {!! Form::label('graduation_time', trans('staff.毕业时间'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('graduation_time', $user->userExt->graduation_time ?? old('graduation_time'), [
                                'class' => 'form-control date',
                                'placeholder' => trans('app.请输入', ['value' => trans('staff.毕业时间')]),
                                'disabled' => true,
                                ]) !!}
                            </div>
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('entry_time') }}</span>
                    </div>

                    <div class="form-group @if (!empty($errors->first('specialty'))) has-error @endif">
                        {!! Form::label('specialty', trans('staff.专业'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('specialty', isset($user->userExt->specialty) ? $user->userExt->specialty: old('specialty'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.专业')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('specialty') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('degree'))) has-error @endif">
                        {!! Form::label('degree', trans('staff.学位'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('degree', isset($user->userExt->degree) ? $user->userExt->degree : old('degree'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.学位')]),
                            'disabled' => true,
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
                                    <th>起止年月</th>
                                    <th>期限</th>
                                    <th>在何处工作</th>
                                    <th>职务</th>
                                    <th>月收入</th>
                                    <th>直属上司</th>
                                    <th>联系电话</th>
                                </tr>
                                </thead>
                                <tbody>
                                @for($i = 0; $i <= \App\Models\StaffManage\Entry::CREATE_WORK_HISTORY_NUM; $i++)
                                    <tr>
                                        <td>{{!empty($workHistory[$i]['time']) ? $workHistory[$i]['time'] : ''}}</td>
                                        <td>{{!empty($workHistory[$i]['deadline']) ? $workHistory[$i]['deadline'] : ''}}</td>
                                        <td>{{!empty($workHistory[$i]['work_place']) ? $workHistory[$i]['work_place'] : ''}}</td>
                                        <td>{{!empty($workHistory[$i]['position']) ? $workHistory[$i]['position'] : ''}}</td>
                                        <td>{{!empty($workHistory[$i]['income']) ? $workHistory[$i]['income'] : ''}}</td>
                                        <td>{{!empty($workHistory[$i]['boss']) ? $workHistory[$i]['boss'] : ''}}</td>
                                        <td>{{!empty($workHistory[$i]['phone']) ? $workHistory[$i]['phone'] : ''}}</td>
                                    </tr>
                                @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('project_empiric'))) has-error @endif">
                        {!! Form::label('project_empiric', trans('staff.项目经验'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::textarea('project_empiric', !empty($user->userExt->project_empiric) ? $user->userExt->project_empiric : old('project_empiric'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.项目经验')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('project_empiric') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('awards'))) has-error @endif">
                        {!! Form::label('awards', trans('staff.获奖情况'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::textarea('awards', !empty($user->userExt->awards) ? $user->userExt->awards : '', [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.获奖情况')]),
                            'disabled' => true,
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
                            {!! Form::text('card_id', !empty($user->userExt->card_id) ? $user->userExt->card_id : old('card_id'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.身份证号码')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('card_id') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('card_address'))) has-error @endif">
                        {!! Form::label('card_address', trans('staff.身份证地址'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('card_address', !empty($user->userExt->card_address) ? $user->userExt->card_address : old('card_address'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.身份证地址')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('card_address') }}</span>
                        </div>
                    </div>

                    <div class="form-group @if (!empty($errors->first('census'))) has-error @endif">
                        {!! Form::label('census', trans('staff.户籍类型'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('census', !empty($user->userExt->census) ? $user->userExt->census : old('census'), [
                            'class' => 'form-control',
                            'placeholder' => trans('app.请输入', ['value' => trans('staff.户籍类型')]),
                            'disabled' => true,
                            ]) !!}
                            <span class="help-block m-b-none">{{ $errors->first('census') }}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('firm_call', trans('app.是否公司挂靠'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            @foreach(\App\Models\UserExt::$firmCall as $k => $v)
                                <label class="radio-inline i-checks">
                                    <input type="radio" disabled="disabled" name="firm_call" value="{{$k}}" @if($k === (int)($user->userExt->firm_call ?? 0)) checked @endif> {{ $v }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-5">
                            <a href="{{ route('staff.list') }}"
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

