@extends('layouts.base')
@section('base')
<div class="wrapper wrapper-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-38">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{ $title ?? trans('staff.入职信息填写') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('flash::message')
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane active">
                                    <div class="ibox-content fill-content">
                                        {!! Form::open(['class' => 'form-horizontal']) !!}

                                        <div class="form-group">
                                            {!! Form::label('name', trans('staff.姓名'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {{Form::hidden('entry[fill_id]', $entryS->entry_id)}}
                                                {{Form::hidden('entry[token]', $sign)}}
                                                {!! Form::text('username', $entryS->name  ,['class' => 'form-control', 'disabled']) !!}
                                            </div>

                                            {!! Form::label('dept_id', trans('staff.所属部门'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('dept_id', $dept[$entryS->dept_id] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('sex', trans('staff.性别'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('username', \App\Models\UserExt::$sex[$entryS->sex] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                                            </div>

                                            {!! Form::label('job_name', trans('staff.岗位名称'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('username', $entryS->job_name ,['class' => 'form-control', 'disabled']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('mobile', trans('staff.手机号码'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('username', $entryS->mobile ,['class' => 'form-control', 'disabled']) !!}
                                            </div>

                                            {!! Form::label('email', trans('staff.邮箱'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('username', $entryS->email ,['class' => 'form-control', 'disabled']) !!}
                                            </div>
                                        </div>

                                        {{--基本信息--}}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.基本信息')}}</h2</label>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group @if (!empty($errors->first('entry.birthday'))) has-error @endif">
                                            {!! Form::label('birthday', trans('staff.生日'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    {!! Form::text('entry[birthday]', !empty($entry->birthday) ? date('Y-m-d', strtotime($entry->birthday)) : ($cache->birthday ?? ''), [
                                                    'class' => 'form-control date',
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('entry.birthday') }}</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <i style="color: red">*</i>
                                                @foreach(\App\Models\StaffManage\Entry::$birthdayType as $k => $v)
                                                    <label style="margin-left: 1em" class="radio-inline i-checks">
                                                        <input type="radio" name="entry[birthday_type]" value="{{$k}}" @if($k === (int)($cache->birthday_type ?? \App\Models\StaffManage\Entry::GREGORIAN_CALENDAR)) checked @endif> {{ $v }}
                                                    </label>
                                                @endforeach
                                            </div>

                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.ethnic_id'))) has-error @endif">
                                            {!! Form::label('ethnic_id', trans('staff.民族'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('entry[ethnic_id]', $ethnic, !empty($entry->ethnic_id) ? $entry->ethnic_id : ($cache->ethnic_id ?? ''), [
                                                'class' => 'form-control js-select2-single',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.民族')]),
                                                'required' => true,
                                                ]) !!}

                                                <span class="help-block m-b-none">{{ $errors->first('entry.ethnic_id') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.birthplace'))) has-error @endif">
                                            {!! Form::label('birthplace', trans('staff.籍贯'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('entry[birthplace]', !empty($entry->birthplace) ? $entry->birthplace: ($cache->birthplace ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.籍贯')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.birthplace') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.political_id'))) has-error @endif">
                                            {!! Form::label('political_id', trans('staff.政治面貌'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('entry[political_id]', \App\Models\UserExt::$political, !empty($entry->political_id) ? $entry->political_id : ($cache->political_id ?? ''), [
                                                'class' => 'form-control js-select2-single',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.政治面貌')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.political_id') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.marital_status'))) has-error @endif">
                                            {!! Form::label('marital_status', trans('staff.婚姻状况'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('entry[marital_status]', \App\Models\UserExt::$marital, !empty($entry->marital_status) ? $entry->marital_status : ($cache->marital_status ?? ''), [
                                                'class' => 'form-control js-select2-single',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.婚姻状况')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.marital_status') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.blood_type'))) has-error @endif">
                                            {!! Form::label('blood_type', trans('staff.血型'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('entry[blood_type]', \App\Models\UserExt::$blood, !empty($entry->blood_type) ? $entry->blood_type: ($cache->blood_type ?? ''), [
                                                'class' => 'form-control js-select2-single',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.血型')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.blood_type') }}</span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.genus_id'))) has-error @endif">
                                            {!! Form::label('genus_id', trans('staff.属相'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('entry[genus_id]', \App\Models\UserExt::$genus, !empty($entry->genus_id) ? $entry->genus_id : ($cache->genus_id ?? ''), [
                                                'class' => 'form-control js-select2-single',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.属相')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.genus_id') }}</span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.constellation_id'))) has-error @endif">
                                            {!! Form::label('constellation_id', trans('staff.星座'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('entry[constellation_id]', \App\Models\UserExt::$constellation, !empty($entry->constellation_id) ? $entry->constellation_id : ($cache->constellation_id ?? ''), [
                                                'class' => 'form-control js-select2-single',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.星座')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.constellation_id') }}</span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.height'))) has-error @endif">
                                            {!! Form::label('dept', trans('staff.身高'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::number('entry[height]', !empty($entry->height) ? $entry->height: ($cache->height ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.身高')]),
                                                'step' => 0.1
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.height') }}</span>
                                            </div>
                                            <div class="col-sm-2">
                                                <span class="help-block m-b-none">
                                                    <i class="fa fa-info-circle"></i> {{ trans('单位：厘米(cm)') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.weight'))) has-error @endif">
                                            {!! Form::label('weight', trans('staff.体重'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::number('entry[weight]', !empty($entry->weight) ? $entry->weight: ($cache->weight ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.体重')]),
                                                'step' => 0.1
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.weight') }}</span>
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
                                                {!! Form::text('entry[salary_card]', !empty($entry->salary_card) ? $entry->salary_card : ($cache->salary_card ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.工资卡')]),

                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.salary_card') }}</span>
                                            </div>
                                            <div class="row">
                                                <i style="color: red">*</i>
                                                <i style="margin-left: 1em" class="fa fa-info-circle"></i> {{ trans('请填写本人招商银行广州储蓄卡账号') }}
                                            </div>
                                        </div>
                                            {{--保存信息--}}
                                            <div class="form-group">
                                                <div class="col-sm-4 col-sm-offset-9">
                                                    {!! Form::button(trans('staff.保存信息草稿'), ['class' => 'btn btn-info', 'id' => 'save_info_1']) !!}</div>
                                            </div>
                                        {{--联系信息--}}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.联系信息')}}</h2</label>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group @if (!empty($errors->first('entry.used_email'))) has-error @endif">
                                            {!! Form::label('used_email', trans('staff.邮箱'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::email('entry[used_email]', !empty($entry->used_email) ? $entry->used_email: ($cache->used_email ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.邮箱')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.used_email') }}</span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.qq'))) has-error @endif">
                                            {!! Form::label('qq', trans('staff.QQ号码'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('entry[qq]', !empty($entry->qq) ? $entry->qq : ($cache->qq ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.QQ号码')]),

                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.qq') }}</span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.live_address'))) has-error @endif">
                                            {!! Form::label('live_address', trans('staff.目前住址'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('entry[live_address]', !empty($entry->live_address) ? $entry->live_address : ($cache->live_address ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.目前住址')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.live_address') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.family_num'))) has-error @endif">
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
                                                            <td><input name="entry[family_num][{{$i}}][name]" value="{{!is_array($entry->family_num) && !empty(json_decode($entry->family_num, true)[$i]['name']) ? json_decode($entry->family_num, true)[$i]['name'] : ($cache->family_num[$i]['name'] ?? '')}}" style="width: 6em;" type="text"></td>
                                                            <td><input name="entry[family_num][{{$i}}][age]" value="{{!is_array($entry->family_num) &&!empty(json_decode($entry->family_num, true)[$i]['age']) ? json_decode($entry->family_num, true)[$i]['age'] : ($cache->family_num[$i]['age'] ?? '')}}" style="width: 3em;" type="text"></td>
                                                            <td><input name="entry[family_num][{{$i}}][relation]" value="{{!is_array($entry->family_num) &&!empty(json_decode($entry->family_num, true)[$i]['relation']) ? json_decode($entry->family_num, true)[$i]['relation'] : ($cache->family_num[$i]['relation'] ?? '')}}" style="width: 100%;" type="text"></td>
                                                            <td><input name="entry[family_num][{{$i}}][position]" value="{{!is_array($entry->family_num) &&!empty(json_decode($entry->family_num, true)[$i]['position']) ? json_decode($entry->family_num, true)[$i]['position'] : ($cache->family_num[$i]['position'] ?? '')}}" style="width: 6em;" type="text"></td>
                                                            <td><input name="entry[family_num][{{$i}}][phone]" value="{{!is_array($entry->family_num) &&!empty(json_decode($entry->family_num, true)[$i]['phone']) ? json_decode($entry->family_num, true)[$i]['phone'] : ($cache->family_num[$i]['phone'] ?? '')}}" style="width: 8em;" type="text"></td>
                                                        </tr>
                                                    @endfor

                                                    </tbody>
                                                </table>
                                                <span class="help-block m-b-none">{{ $errors->first('entry.family_num') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.urgent_name'))) has-error @endif">
                                            {!! Form::label('urgent_name', trans('staff.紧急联系人姓名'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('entry[urgent_name]', !empty($entry->urgent_name) ? $entry->urgent_name : ($cache->urgent_name ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.紧急联系人姓名')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.urgent_name') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.urgent_bind'))) has-error @endif">
                                            {!! Form::label('urgent_bind', trans('staff.与紧急联系人的关系'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('entry[urgent_bind]', !empty($entry->urgent_bind) ? $entry->urgent_bind : ($cache->urgent_bind ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.与紧急联系人的关系')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.urgent_bind') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.urgent_tel'))) has-error @endif">
                                            {!! Form::label('urgent_tel', trans('staff.紧急联系人电话'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('entry[urgent_tel]', !empty($entry->urgent_tel) ? $entry->urgent_tel : ($cache->urgent_tel ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.紧急联系人电话')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.urgent_tel') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>
                                            {{--保存信息--}}
                                            <div class="form-group">
                                                <div class="col-sm-4 col-sm-offset-9">
                                                    {!! Form::button(trans('staff.保存信息草稿'), ['class' => 'btn btn-info', 'id' => 'save_info_2']) !!}</div>
                                            </div>
                                        {{--学历信息--}}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.学历信息')}}</h2</label>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group @if (!empty($errors->first('entry.education_id'))) has-error @endif">
                                            {!! Form::label('education_id', trans('staff.最高学历'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('entry[education_id]', \App\Models\UserExt::$education, !empty($entry->education_id) ? $entry->education_id: ($cache->education_id ?? ''), [
                                                'class' => 'form-control js-select2-single',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.最高学历')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.education_id') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.school_id'))) has-error @endif">
                                            {!! Form::label('school_id', trans('staff.毕业学校'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('entry[school_id]', $school, !empty($entry->school_id) ? $entry->school_id : ($cache->school_id ?? ''), [
                                                'class' => 'form-control js-select2-single',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.毕业学校')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.school_id') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group  @if (!empty($errors->first('entry.graduation_time'))) has-error @endif">
                                            {!! Form::label('graduation_time', trans('staff.毕业时间'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="color: red"><i class="fa fa-calendar"></i></span>
                                                    {!! Form::text('entry[graduation_time]', !empty($entry->graduation_time) ? date('Y-m-d', strtotime($entry->graduation_time)) : ($cache->graduation_time ?? '') , [
                                                    'class' => 'form-control date',
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('entry.graduation_time') }}</span>
                                                </div>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.specialty'))) has-error @endif">
                                            {!! Form::label('specialty', trans('staff.专业'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('entry[specialty]', !empty($entry->specialty) ? $entry->specialty: ($cache->specialty ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.专业')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.specialty') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.degree'))) has-error @endif">
                                            {!! Form::label('entry[degree]', trans('staff.学位'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('entry[degree]', !empty($entry->degree) ? $entry->degree : ($cache->degree ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.学位')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.degree') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>
                                            {{--保存信息--}}
                                            <div class="form-group">
                                                <div class="col-sm-4 col-sm-offset-9">
                                                    {!! Form::button(trans('staff.保存信息草稿'), ['class' => 'btn btn-info', 'id' => 'save_info_3']) !!}</div>
                                            </div>

                                        <div class="hr-line-dashed"></div>

                                        {{--工作信息--}}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.工作信息')}}</h2</label>
                                        </div>

                                        <div class="hr-line-dashed"></div>


                                        <div class="form-group">
                                            {!! Form::label('education_id', trans('staff.工作经历'), ['class' => 'col-sm-1 control-label']) !!}
                                            <div class="col-sm-10">
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
                                                            <td><input name="entry[work_history][{{$i}}][time]" value="{{!is_array($entry->work_history) && !empty(json_decode($entry->work_history, true)[$i]['time']) ? json_decode($entry->work_history, true)[$i]['time'] : ($cache->work_history[$i]['time'] ?? '')}}" style="width: 6em;" type="text"></td>
                                                            <td><input name="entry[work_history][{{$i}}][deadline]" value="{{!is_array($entry->work_history) && !empty(json_decode($entry->work_history, true)[$i]['deadline']) ? json_decode($entry->work_history, true)[$i]['deadline'] : ($cache->work_history[$i]['deadline'] ?? '')}}" style="width: 3em;" type="text"></td>
                                                            <td><input name="entry[work_history][{{$i}}][work_place]" value="{{!is_array($entry->work_history) && !empty(json_decode($entry->work_history, true)[$i]['work_place']) ? json_decode($entry->work_history, true)[$i]['work_place'] : ($cache->work_history[$i]['work_place'] ?? '')}}" style="width: 100%;" type="text"></td>
                                                            <td><input name="entry[work_history][{{$i}}][position]" value="{{!is_array($entry->work_history) && !empty(json_decode($entry->work_history, true)[$i]['position']) ? json_decode($entry->work_history, true)[$i]['position'] : ($cache->work_history[$i]['position'] ?? '')}}" style="width: 5em;" type="text"></td>
                                                            <td><input name="entry[work_history][{{$i}}][income]" value="{{!is_array($entry->work_history) && !empty(json_decode($entry->work_history, true)[$i]['income']) ? json_decode($entry->work_history, true)[$i]['income'] : ($cache->work_history[$i]['income'] ?? '')}}" style="width: 4em;" type="text"></td>
                                                            <td><input name="entry[work_history][{{$i}}][boss]" value="{{!is_array($entry->work_history) && !empty(json_decode($entry->work_history, true)[$i]['boss']) ? json_decode($entry->work_history, true)[$i]['boss'] : ($cache->work_history[$i]['boss'] ?? '')}}" style="width: 100%;" type="text"></td>
                                                            <td><input name="entry[work_history][{{$i}}][phone]"  value="{{!is_array($entry->work_history) && !empty(json_decode($entry->work_history, true)[$i]['phone']) ? json_decode($entry->work_history, true)[$i]['phone'] : ($cache->work_history[$i]['phone'] ?? '')}}" style="width: 8em;" type="text"></td>
                                                        </tr>
                                                    @endfor
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.project_empiric'))) has-error @endif">
                                            {!! Form::label('project_empiric', trans('staff.项目经验'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-6">
                                                {!! Form::textarea('entry[project_empiric]', !empty($entry->project_empiric) ? $entry->project_empiric : ($cache->project_empiric ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.项目经验')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.project_empiric') }}</span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.awards'))) has-error @endif">
                                            {!! Form::label('awards', trans('staff.获奖情况'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-6">
                                                {!! Form::textarea('entry[awards]', !empty($entry->awards) ? $entry->awards : ($cache->awards ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.获奖情况')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.awards') }}</span>
                                            </div>
                                        </div>
                                            {{--保存信息--}}
                                            <div class="form-group">
                                                <div class="col-sm-4 col-sm-offset-9">
                                                    {!! Form::button(trans('staff.保存信息草稿'), ['class' => 'btn btn-info', 'id' => 'save_info_4']) !!}</div>
                                            </div>
                                        {{--户籍档案信息--}}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.户籍档案信息')}}</h2</label>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group @if (!empty($errors->first('entry.card_id'))) has-error @endif">
                                            {!! Form::label('card_id', trans('staff.身份证号码'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('entry[card_id]', !empty($entry->card_id) ? $entry->card_id: ($cache->card_id ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.身份证号码')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.card_id') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.card_address'))) has-error @endif">
                                            {!! Form::label('card_address', trans('staff.身份证地址'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('entry[card_address]', !empty($entry->card_address) ? $entry->card_address : ($cache->card_address ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.身份证地址')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.card_address') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('entry.census'))) has-error @endif">
                                            {!! Form::label('census', trans('staff.户籍类型'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('entry[census]', !empty($entry->census) ? $entry->census : ($cache->census ?? ''), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.户籍类型')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('entry.census') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('firm_call', trans('app.是否公司挂靠'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">

                                                @foreach(\App\Models\UserExt::$firmCall as $k => $v)
                                                    <label class="radio-inline i-checks">
                                                        <input type="radio" name="entry[firm_call]" value="{{$k}}" @if($k === (int)($cache->firm_call ?? 0)) checked @endif> {{ $v }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group">
                                            <div class="col-sm-4 col-sm-offset-4">
                                                {!! Form::button(trans('staff.保存信息草稿'), ['class' => 'btn btn-info', 'id' => 'save_info_5']) !!}
                                                {!! Form::button(trans('staff.确认无误提交'), ['class' => 'btn btn-primary', 'id' => 'show_admit_modal']) !!}
                                            </div>
                                        </div>

                                        {{--部门信息弹窗--}}
                                        <div class="modal inmodal" id="admit-modal" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content animated fadeIn">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                                                    class="sr-only">Close</span></button>
                                                        <h4 class="modal-title">员工承诺书</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div>
                                                            <p>本人承诺：</p>
                                                            <p>1、所提供及填写的资料均属实，如有虚假的，本人愿承担一切法律后果。</p>
                                                            <p>2、入职时已了解、知悉并接受公司的各项规章制度，并且当公司制度依据公司司情做出合法修改或变更的，本人将同样予以遵守。</p>
                                                            <p>3、本人不存在与其他单位签订竞业禁止协议的情形，本人与公司签订并履行劳动合同将不会违反任何其他劳动协议或其他约定。否则，由此产生的任何后果，由本人承担全部责任。</p>
                                                            <p>4、本人入职后将不从事任何影响公司利益的兼职行为。</p>
                                                            <p>5、严格遵守薪酬保密制度，不在公司内、外讨论或透露个人薪酬资料。</p>
                                                            <p>6、体验内部游戏时严格遵循内玩管理规定，内部帐号不与他人共享、内部元宝不变相与玩家交易等。</p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('app.关闭') }}</button>
                                                        {!! Form::submit(trans('staff.同意并提交'), ['class' => 'btn btn-primary']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{--部门信息弹窗end--}}

@include('widget.bootbox')
@include('widget.icheck')
@include('widget.select2')
@include('widget.datepicker')
@section('scripts-last')
    <script>
        $(function() {
            $('#js-select2-single').select2();

            $('#show_admit_modal').click(function () {
                $('#admit-modal').modal('show');
            });

            $("button[id^=save_info_]").click(function () {
                var data = $('.fill-content').find("*[name^='entry']").serialize();
                $.ajax({
                    type:"get",
                    url:"{{ route('entry.save') }}",
                    async:false,
                    data:data,
                    dataType:"json",
                    success: function (msg){
                        if(msg.error < 0) {
                            bootbox.alert('保存失败');
                        }else{
                            bootbox.alert('保存成功');
                        }
                    },
                    error: function(err) {
                        bootbox.alert("错误的保存数据");
                    }
                });
            })

        });
    </script>
@endsection