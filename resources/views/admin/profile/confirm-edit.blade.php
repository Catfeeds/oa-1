@extends('admin.profile.profile')

@section('content-profile')

    {!! Form::open(['class' => 'form-horizontal']) !!}
    {{--分割线--}}
    <div class="col-sm-6 b-r">

        <div class="form-group @if (!empty($errors->first('school_id'))) has-error @endif">
            {!! Form::label('school_id', trans('app.毕业学校'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                <select class="js-select2-single form-control" name="school_id" >
                    <option value=" ">{!! trans('app.请选择毕业学校') !!}</option>
                    @foreach($school as $k => $v)
                        <option value="{{ $k }}" @if($k === $user->userExt->school) selected="selected" @endif>{{ $school[$k] }}</option>
                    @endforeach
                </select>
            </div>
            <span class="help-block m-b-none">{{ $errors->first('school_id') }}</span>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group  @if (!empty($errors->first('graduation_time'))) has-error @endif">
            {!! Form::label('graduation_time', trans('app.毕业时间'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="input-daterange input-group col-sm-6">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                {!! Form::text('graduation_time',$user->userExt->graduation_time ?? old('graduation_time'), [
                'class' => 'form-control date',
                'placeholder' => trans('app.请输入', ['value' => trans('毕业时间')]),
                'required' => true,
                ]) !!}
            </div>
            <span class="help-block m-b-none">{{ $errors->first('graduation_time') }}</span>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group  @if (!empty($errors->first('education_id'))) has-error @endif">
            {!! Form::label('education_id', trans('app.学历'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                <select class="js-select2-single form-control" name="education_id">
                    <option value="">{!! trans('app.请选择学历') !!}</option>
                    @foreach(\App\Models\UserExt::$education as $k => $v)
                        <option value="{{ $k }}" @if($k === $user->userExt->education_id) selected="selected" @endif>{{ \App\Models\UserExt::$education[$k] }}</option>
                    @endforeach
                </select>
            </div>
            <span class="help-block m-b-none">{{ $errors->first('education_id') }}</span>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group  @if (!empty($errors->first('sex'))) has-error @endif">
            {!! Form::label('sex', trans('app.性别'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                <select class="js-select2-single form-control" name="sex" >
                    <option value="">{!! trans('app.请选择性别') !!}</option>
                    @foreach(\App\Models\UserExt::$sex as $k => $v)
                        <option value="{{ $k }}" @if($k === $user->userExt->sex) selected="selected" @endif>{{ \App\Models\UserExt::$sex[$k] }}</option>
                    @endforeach
                </select>
            </div>
            <span class="help-block m-b-none">{{ $errors->first('sex') }}</span>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('constellation_id'))) has-error @endif">
            {!! Form::label('constellation_id', trans('app.星座'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                <select class="js-select2-single form-control" name="constellation_id">
                    <option value="">{!! trans('app.请选择星座') !!}</option>
                    @foreach(\App\Models\UserExt::$constellation as $k => $v)
                        <option value="{{ $k }}" @if($k === $user->userExt->constellation_id) selected="selected" @endif>{{ \App\Models\UserExt::$constellation[$k] }}</option>
                    @endforeach
                </select>
            </div>
            <span class="help-block m-b-none">{{ $errors->first('constellation_id') }}</span>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('blood_type'))) has-error @endif">
            {!! Form::label('blood_type', trans('app.血型'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                <select class="js-select2-single form-control" name="blood_type">
                    <option value="">{!! trans('app.请选择血型') !!}</option>
                    @foreach(\App\Models\UserExt::$blood as $k => $v)
                        <option value="{{ $k }}" @if($k === $user->userExt->blood_type) selected="selected" @endif>{{ \App\Models\UserExt::$blood[$k] }}</option>
                    @endforeach
                </select>
            </div>
            <span class="help-block m-b-none">{{ $errors->first('blood_type') }}</span>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('qq'))) has-error @endif">
            {!! Form::label('qq', trans('app.QQ号码'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('qq', isset($user->userExt->qq) ? $user->userExt->qq : old('qq'), [
                'class' => 'form-control',
                'placeholder' => trans('app.请输入', ['value' => trans('app.QQ号码')]),
                'required' => true,

                ]) !!}
                <span class="help-block m-b-none">{{ $errors->first('qq') }}</span>
            </div>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('age'))) has-error @endif">
            {!! Form::label('age', trans('app.年龄'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::number('age', isset($user->userExt->age) ? $user->userExt->age : old('age'), [
                'class' => 'form-control',
                'placeholder' => trans('app.请输入', ['value' => trans('app.年龄')]),
                'required' => true,
                ]) !!}
                <span class="help-block m-b-none">{{ $errors->first('age') }}</span>
            </div>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('card_id'))) has-error @endif">
            {!! Form::label('card_id', trans('app.身份证号码'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('card_id', isset($user->userExt->card_id) ? $user->userExt->card_id : old('card_id'), [
                'class' => 'form-control',
                'placeholder' => trans('app.请输入', ['value' => trans('app.身份证号码')]),
                'required' => true,
                ]) !!}
                <span class="help-block m-b-none">{{ $errors->first('card_id') }}</span>
            </div>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('card_address'))) has-error @endif">
            {!! Form::label('card_address', trans('app.身份证地址'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('card_address', isset($user->userExt->card_address) ? $user->userExt->card_address : old('card_address'), [
                'class' => 'form-control',
                'placeholder' => trans('app.请输入', ['value' => trans('app.身份证地址')]),
                'required' => true,
                ]) !!}
                <span class="help-block m-b-none">{{ $errors->first('card_address') }}</span>
            </div>
        </div>

    </div>
    {{--分割线--}}
    <div class="col-sm-6">
        <div class="form-group  @if (!empty($errors->first('born'))) has-error @endif">
            {!! Form::label('born', trans('app.出生日期'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="input-daterange input-group col-sm-6">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                {!! Form::text('born',$user->userExt->born ?? old('born'), [
                'class' => 'form-control date',
                'placeholder' => trans('app.请输入', ['value' => trans('app.出生日期')]),
                'required' => true,
                ]) !!}
            </div>
            <span class="help-block m-b-none">{{ $errors->first('born') }}</span>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('birthplace'))) has-error @endif">
            {!! Form::label('birthplace', trans('app.籍贯'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('birthplace', isset($user->userExt->birthplace) ? $user->userExt->birthplace : old('birthplace'), [
                'class' => 'form-control',
                'placeholder' => trans('app.请输入', ['value' => trans('app.籍贯')]),
                'required' => true,
                ]) !!}
                <span class="help-block m-b-none">{{ $errors->first('birthplace') }}</span>
            </div>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('marital_status'))) has-error @endif">
            {!! Form::label('marital_status', trans('app.婚姻状况'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                <select class="js-select2-single form-control" name="marital_status" >
                    <option value="">{!! trans('app.请选择婚姻状况') !!}</option>
                    @foreach(\App\Models\UserExt::$marital as $k => $v)
                        <option value="{{ $k }}" @if($k === $user->userExt->marital_status) selected="selected" @endif>{{ \App\Models\UserExt::$marital[$k] }}</option>
                    @endforeach
                </select>
            </div>
            <span class="help-block m-b-none">{{ $errors->first('marital_status') }}</span>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('family_num'))) has-error @endif">
            {!! Form::label('family_num', trans('app.家庭成员人数'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::number('family_num', isset($user->userExt->family_num) ? $user->userExt->family_num : old('family_num'), [
                'class' => 'form-control',
                'placeholder' => trans('app.请输入', ['value' => trans('app.家庭成员人数')]),
                'required' => true,
                ]) !!}
                <span class="help-block m-b-none">{{ $errors->first('family_num') }}</span>
            </div>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('census'))) has-error @endif">
            {!! Form::label('alias', trans('app.户籍类型'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('census', isset($user->userExt->census) ? $user->userExt->census : old('census'), [
                'class' => 'form-control',
                'placeholder' => trans('app.请输入', ['value' => trans('app.户籍类型')]),
                'required' => true,
                ]) !!}
                <span class="help-block m-b-none">{{ $errors->first('census') }}</span>
            </div>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('live_address'))) has-error @endif">
            {!! Form::label('live_address', trans('app.居住地址'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('live_address', isset($user->userExt->live_address) ? $user->userExt->live_address : old('live_address'), [
                'class' => 'form-control',
                'placeholder' => trans('app.请输入', ['value' => trans('app.居住地址')]),
                'required' => true,
                ]) !!}
                <span class="help-block m-b-none">{{ $errors->first('live_address') }}</span>
            </div>
        </div>


        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('urgent_name'))) has-error @endif">
            {!! Form::label('urgent_name', trans('app.紧急联系人姓名'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('urgent_name', isset($user->userExt->urgent_name) ? $user->userExt->urgent_name : old('urgent_name'), [
                'class' => 'form-control',
                'placeholder' => trans('app.请输入', ['value' => trans('app.紧急联系人姓名')]),
                'required' => true,
                ]) !!}
                <span class="help-block m-b-none">{{ $errors->first('live_address') }}</span>
            </div>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('urgent_tel'))) has-error @endif">
            {!! Form::label('urgent_tel', trans('app.紧急联系人电话'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('urgent_tel', isset($user->userExt->urgent_tel) ? $user->userExt->urgent_tel : old('urgent_tel'), [
                'class' => 'form-control',
                'placeholder' => trans('app.请输入', ['value' => trans('app.紧急联系人电话')]),
                'required' => true,
                ]) !!}
                <span class="help-block m-b-none">{{ $errors->first('urgent_tel') }}</span>
            </div>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group  @if (!empty($errors->first('entry_time'))) has-error @endif">
            {!! Form::label('entry_time', trans('app.入职时间'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="input-daterange input-group col-sm-6">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                {!! Form::text('entry_time',$user->userExt->entry_time ?? old('entry_time'), [
                'class' => 'form-control date',
                'placeholder' => trans('app.请输入', ['value' => trans('app.入职时间')]),
                'required' => true,
                ]) !!}
            </div>
            <span class="help-block m-b-none">{{ $errors->first('entry_time') }}</span>
        </div>

        <div class="hr-line-dashed"></div>

        <div class="form-group @if (!empty($errors->first('salary_card'))) has-error @endif">
            {!! Form::label('salary_card', trans('app.工资卡'), ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('salary_card', isset($user->userExt->salary_card) ? $user->userExt->salary_card : old('salary_card'), [
                'class' => 'form-control',
                'placeholder' => trans('app.请输入', ['value' => trans('app.工资卡')]),
                'required' => true,
                ]) !!}
                <span class="help-block m-b-none">{{ $errors->first('salary_card') }}</span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-5">
            {!! Form::submit(trans('app.确认提交'), ['class' => 'btn btn-primary']) !!}
        </div>
    </div>

    {!! Form::close() !!}

@endsection

@include('widget.icheck')
@include('widget.select2')
@include('widget.datepicker')

@section('scripts-last')
    <script>
        $(function() {
            $('.js-select2-single').select2();

        });
    </script>
@endsection
