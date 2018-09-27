@extends('layouts.base')
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
                                    <div class="ibox-content profile-content">
                                        {!! Form::open(['class' => 'form-horizontal']) !!}

                                        <div class="form-group">
                                            {!! Form::label('name', trans('staff.姓名'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('username', $entry->name  ,['class' => 'form-control', 'disabled']) !!}
                                            </div>

                                            {!! Form::label('dept_id', trans('staff.所属部门'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('dept_id', $dept[$entry->dept_id] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('sex', trans('staff.性别'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('username', \App\Models\UserExt::$sex[$entry->sex] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                                            </div>

                                            {!! Form::label('job_name', trans('staff.岗位名称'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('username', $entry->job_name ,['class' => 'form-control', 'disabled']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('mobile', trans('staff.手机号码'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('username', $entry->mobile ,['class' => 'form-control', 'disabled']) !!}
                                            </div>

                                            {!! Form::label('email', trans('staff.邮箱'), ['class' => 'col-sm-2 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('username', $entry->email ,['class' => 'form-control', 'disabled']) !!}
                                            </div>
                                        </div>

                                        {{--基本信息--}}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.基本信息')}}</h2</label>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group @if (!empty($errors->first('card_id'))) has-error @endif">
                                            {!! Form::label('card_id', trans('staff.身份证号码'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('card_id', isset($entry->card_id) ? $entry->card_id: old('card_id'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.身份证号码')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('card_id') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('card_address'))) has-error @endif">
                                            {!! Form::label('card_address', trans('staff.身份证地址'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('card_address', isset($entry->card_address) ? $entry->card_address: old('card_address'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.身份证地址')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('card_address') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('ethnic'))) has-error @endif">
                                            {!! Form::label('ethnic', trans('staff.民族'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('ethnic', isset($entry->ethnic) ? $entry->ethnic: old('ethnic'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.民族')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('ethnic') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('birthplace'))) has-error @endif">
                                            {!! Form::label('birthplace', trans('staff.籍贯'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('birthplace', isset($entry->birthplace) ? $entry->birthplace: old('birthplace'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.籍贯')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('birthplace') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('political'))) has-error @endif">
                                            {!! Form::label('political', trans('staff.政治面貌'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('political', isset($entry->political) ? $entry->political: old('political'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.政治面貌')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('political') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('census'))) has-error @endif">
                                            {!! Form::label('census', trans('staff.户籍类型'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('census', isset($entry->census) ? $entry->census: old('census'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.户籍类型')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('census') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('marital_status'))) has-error @endif">
                                            {!! Form::label('marital_status', trans('staff.婚姻状况'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('marital_status', \App\Models\UserExt::$marital, isset($entry->marital_status) ? $entry->marital_status: old('marital_status'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.婚姻状况')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('marital_status') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('blood_type'))) has-error @endif">
                                            {!! Form::label('blood_type', trans('staff.血型'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('blood_type', \App\Models\UserExt::$blood, isset($entry->blood_type) ? $entry->blood_type: old('blood_type'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.血型')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('blood_type') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('genus_id'))) has-error @endif">
                                            {!! Form::label('genus_id', trans('staff.属相'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('genus_id', \App\Models\UserExt::$genus, isset($entry->genus_id) ? $entry->genus_id: old('genus_id'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.属相')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('genus_id') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('constellation_id'))) has-error @endif">
                                            {!! Form::label('constellation_id', trans('staff.星座'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('constellation_id', \App\Models\UserExt::$constellation, isset($entry->constellation_id) ? $entry->constellation_id: old('constellation_id'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.星座')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('constellation_id') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('height'))) has-error @endif">
                                            {!! Form::label('dept', trans('staff.身高'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('height', isset($entry->height) ? $entry->height: old('height'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.身高')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('height') }}</span>
                                            </div>
                                            <div class="col-sm-2">
                                                <span class="help-block m-b-none">
                                                    <i class="fa fa-info-circle"></i> {{ trans('单位：厘米(cm)') }}
                                                </span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('weight'))) has-error @endif">
                                            {!! Form::label('weight', trans('staff.体重'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('weight', isset($entry->weight) ? $entry->weight: old('weight'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.体重')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('weight') }}</span>
                                            </div>
                                            <div class="col-sm-2">
                                                <span class="help-block m-b-none">
                                                    <i class="fa fa-info-circle"></i> {{ trans('单位：公斤/千克(kg)') }}
                                                </span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        {{--联系信息--}}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.联系信息')}}</h2</label>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group @if (!empty($errors->first('qq'))) has-error @endif">
                                            {!! Form::label('qq', trans('staff.QQ号码'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('qq', isset($entry->qq) ? $entry->qq: old('qq'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.QQ号码')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('qq') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('live_address'))) has-error @endif">
                                            {!! Form::label('live_address', trans('staff.目前住址'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('live_address', isset($entry->live_address) ? $entry->live_address: old('live_address'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.目前住址')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('live_address') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('family_num'))) has-error @endif">
                                            {!! Form::label('family_num', trans('staff.家庭成员人数'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('family_num', isset($entry->family_num) ? $entry->family_num: old('family_num'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.家庭成员人数')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('family_num') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('urgent_name'))) has-error @endif">
                                            {!! Form::label('urgent_name', trans('staff.紧急联系人姓名'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('urgent_name', isset($entry->urgent_name) ? $entry->urgent_name: old('urgent_name'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.紧急联系人姓名')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('urgent_name') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('urgent_bind'))) has-error @endif">
                                            {!! Form::label('urgent_bind', trans('staff.与紧急联系人的关系'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('urgent_bind', isset($entry->urgent_bind) ? $entry->urgent_bind: old('urgent_bind'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.与紧急联系人的关系')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('urgent_bind') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('urgent_tel'))) has-error @endif">
                                            {!! Form::label('urgent_tel', trans('staff.紧急联系人电话'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('urgent_tel', isset($entry->urgent_tel) ? $entry->urgent_tel: old('urgent_tel'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.紧急联系人电话')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('urgent_tel') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        {{--学历信息--}}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.学历信息')}}</h2</label>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group @if (!empty($errors->first('education_id'))) has-error @endif">
                                            {!! Form::label('education_id', trans('staff.最高学历'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('education_id', \App\Models\UserExt::$education, isset($entry->education_id) ? $entry->education_id: old('education_id'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.最高学历')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('education_id') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('school_id'))) has-error @endif">
                                            {!! Form::label('school_id', trans('staff.毕业学校'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::select('school_id', $school, isset($entry->school_id) ? $entry->school_id: old('school_id'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请选择', ['value' => trans('staff.毕业学校')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('school_id') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group  @if (!empty($errors->first('graduation_time'))) has-error @endif">
                                            {!! Form::label('graduation_time', trans('staff.毕业时间'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="input-daterange input-group col-sm-3">
                                                <span class="input-group-addon" style="color: red"><i class="fa fa-calendar"></i></span>
                                                {!! Form::text('graduation_time', $entry->graduation_time ?? old('graduation_time'), [
                                                'class' => 'form-control date',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.毕业时间')]),
                                                'required' => true,
                                                ]) !!}
                                            </div>
                                            <span class="help-block m-b-none">{{ $errors->first('entry_time') }}</span>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('specialty'))) has-error @endif">
                                            {!! Form::label('specialty', trans('staff.专业'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('specialty', isset($entry->specialty) ? $entry->specialty: old('specialty'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.专业')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('specialty') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('degree'))) has-error @endif">
                                            {!! Form::label('degree', trans('staff.学位'), ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('degree', isset($entry->degree) ? $entry->degree: old('degree'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('staff.学位')]),
                                                'required' => true,
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('degree') }}</span>
                                            </div>
                                            <i style="color: red">*</i>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group">
                                            <div class="col-sm-3 col-sm-offset-5">
                                                {!! Form::submit(trans('staff.确认无误提交'), ['class' => 'btn btn-primary']) !!}
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

@include('widget.bootbox')
@include('widget.icheck')
@include('widget.select2')
@include('widget.datepicker')
@section('scripts-last')
    <script>
        $(function() {
            $('#sex').select2();
            $('#marital_status').select2();
            $('#blood_type').select2();
            $('#genus_id').select2();
            $('#constellation_id').select2();
            $('#education_id').select2();
            $('#school_id').select2();
            $('#hire_id').select2();
            $('#firm_id').select2();
            $('#job_id').select2();
            $('#leader_id').select2();
            $('#friend_id').select2();
            $('#tutor_id').select2();
            $('#copy_users').select2();
            $('#nature_id').select2();
        });
    </script>
@endsection