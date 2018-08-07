@extends('layouts.top-nav')

@section('title', $title)
@section('body-class', 'top-navigation')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $title }}</h5>

                            @if(Entrust::can(['user-all', 'user']))
                                <div class="ibox-tools">
                                    <a class="btn btn-xs btn-primary" href="{{ route('user') }}">
                                        {{ trans('app.员工列表') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="ibox-content">

                            {!! Form::open(['class' => 'form-horizontal']) !!}
                            {{--分割线--}}
                            <div class="col-sm-6 b-r">
                                <div class="form-group">
                                    {!! Form::label('username', trans('app.账号'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        {{-- 账号不可编辑 --}}
                                        {!! Form::text('username', isset($user->username) ? $user->username : old('username'), isset($user->username) ? [
                                        'class' => 'form-control',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.账号')]),
                                         
                                        'disabled',
                                        ] : [
                                        'class' => 'form-control',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.账号')]),
                                         
                                        ]) !!}
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    {!! Form::label('alias', trans('app.姓名'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        {{-- 账号不可编辑 --}}
                                        {!! Form::text('username', isset($user->alias) ? $user->alias : old('alias'), isset($user->alias) ? [
                                        'class' => 'form-control',
                                         
                                        'disabled',
                                        ] : [
                                        'class' => 'form-control',
                                         
                                        ]) !!}
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    {!! Form::label('school_id', trans('app.毕业学校'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        <select class="js-select2-single form-control" name="school_id" >
                                            @foreach($school as $k => $v)
                                                <option value="{{ $k }}" @if($k === $user->userExt->school) selected="selected" @endif>{{ $school[$k] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group  @if (!empty($errors->first('graduation_time'))) has-error @endif">
                                    {!! Form::label('graduation_time', trans('app.毕业时间'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="input-daterange input-group col-sm-6">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text('graduation_time',$user->userExt->graduation_time ?? old('graduation_time'), [
                                        'class' => 'form-control date',
                                        'placeholder' => trans('app.请输入', ['value' => trans('毕业时间')]),
                                        ]) !!}
                                    </div>
                                    <span class="help-block m-b-none">{{ $errors->first('graduation_time') }}</span>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    {!! Form::label('education_id', trans('app.学历'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        <select class="js-select2-single form-control" name="education_id" >
                                            @foreach(\App\Models\UserExt::$education as $k => $v)
                                                <option value="{{ $k }}" @if($k === $user->userExt->education_id) selected="selected" @endif>{{ \App\Models\UserExt::$education[$k] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    {!! Form::label('constellation_id', trans('app.星座'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        <select class="js-select2-single form-control" name="constellation_id" >
                                            @foreach(\App\Models\UserExt::$constellation as $k => $v)
                                                <option value="{{ $k }}" @if($k === $user->userExt->constellation_id) selected="selected" @endif>{{ \App\Models\UserExt::$constellation[$k] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    {!! Form::label('blood_type', trans('app.血型'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        <select class="js-select2-single form-control" name="blood_type" >
                                            @foreach(\App\Models\UserExt::$blood as $k => $v)
                                                <option value="{{ $k }}" @if($k === $user->userExt->blood_type) selected="selected" @endif>{{ \App\Models\UserExt::$blood[$k] }}</option>
                                            @endforeach
                                        </select>
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
                                        ]) !!}
                                    </div>
                                    <span class="help-block m-b-none">{{ $errors->first('entry_time') }}</span>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group  @if (!empty($errors->first('turn_time'))) has-error @endif">
                                    {!! Form::label('turn_time', trans('app.转正时间'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="input-daterange input-group col-sm-6">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text('turn_time',$user->userExt->turn_time ?? old('turn_time'), [
                                        'class' => 'form-control date',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.转正时间')]),
                                        ]) !!}
                                    </div>
                                    <span class="help-block m-b-none">{{ $errors->first('turn_time') }}</span>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group  @if (!empty($errors->first('contract_st'))) has-error @endif">
                                    {!! Form::label('contract_st', trans('app.合同开始时间'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="input-daterange input-group col-sm-6">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text('contract_st',$user->userExt->turn_time ?? old('contract_st'), [
                                        'class' => 'form-control date',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.合同开始时间')]),
                                        ]) !!}
                                    </div>
                                    <span class="help-block m-b-none">{{ $errors->first('contract_st') }}</span>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group  @if (!empty($errors->first('contract_et'))) has-error @endif">
                                    {!! Form::label('contract_et', trans('app.合同结束时间'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="input-daterange input-group col-sm-6">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text('contract_et',$user->userExt->turn_time ?? old('contract_et'), [
                                        'class' => 'form-control date',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.合同结束时间')]),
                                        ]) !!}
                                    </div>
                                    <span class="help-block m-b-none">{{ $errors->first('contract_et') }}</span>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group @if (!empty($errors->first('incumbent_num'))) has-error @endif">
                                    {!! Form::label('incumbent_num', trans('app.在职年数'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        {!! Form::number('incumbent_num', isset($user->userExt->incumbent_num) ? $user->userExt->incumbent_num : old('incumbent_num'), [
                                        'class' => 'form-control',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.在职年数')]),
                                        ]) !!}
                                        <span class="help-block m-b-none">{{ $errors->first('incumbent_num') }}</span>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group @if (!empty($errors->first('contract_num'))) has-error @endif">
                                    {!! Form::label('contract_num', trans('app.合同签约次数'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        {!! Form::number('contract_num', isset($user->userExt->contract_num) ? $user->userExt->contract_num : old('contract_num'), [
                                        'class' => 'form-control',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.合同签约次数')]),
                                         
                                        ]) !!}
                                        <span class="help-block m-b-none">{{ $errors->first('contract_num') }}</span>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    {!! Form::label('firm_call', trans('app.是否公司挂靠'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        <select class="js-select2-single form-control" name="education_id" >
                                            @foreach(\App\Models\UserExt::$firmCall as $k => $v)
                                                <option value="{{ $k }}" @if($k === $user->userExt->firm_call) selected="selected" @endif>{{ \App\Models\UserExt::$firmCall[$k] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                            {{--分割线--}}
                            <div class="col-sm-6">

                                <div class="form-group">
                                    {!! Form::label('sex', trans('app.性别'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        <select class="js-select2-single form-control" name="sex" >
                                            @foreach(\App\Models\UserExt::$sex as $k => $v)
                                                <option value="{{ $k }}" @if($k === $user->userExt->sex) selected="selected" @endif>{{ \App\Models\UserExt::$sex[$k] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group @if (!empty($errors->first('age'))) has-error @endif">
                                    {!! Form::label('age', trans('app.年龄'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        {!! Form::number('age', isset($user->userExt->age) ? $user->userExt->age : old('age'), [
                                        'class' => 'form-control',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.年龄')]),
                                         
                                        ]) !!}
                                        <span class="help-block m-b-none">{{ $errors->first('age') }}</span>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group  @if (!empty($errors->first('born'))) has-error @endif">
                                    {!! Form::label('born', trans('app.出生日期'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="input-daterange input-group col-sm-6">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text('born',$user->userExt->born ?? old('born'), [
                                        'class' => 'form-control date',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.出生日期')]),
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
                                        ]) !!}
                                        <span class="help-block m-b-none">{{ $errors->first('birthplace') }}</span>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    {!! Form::label('marital_status', trans('app.婚姻状况'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        <select class="js-select2-single form-control" name="marital_status" >
                                            @foreach(\App\Models\UserExt::$marital as $k => $v)
                                                <option value="{{ $k }}" @if($k === $user->userExt->marital_status) selected="selected" @endif>{{ \App\Models\UserExt::$marital[$k] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group @if (!empty($errors->first('family_num'))) has-error @endif">
                                    {!! Form::label('family_num', trans('app.家庭成员人数'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        {!! Form::number('family_num', isset($user->userExt->family_num) ? $user->userExt->family_num : old('family_num'), [
                                        'class' => 'form-control',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.家庭成员人数')]),
                                         
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
                                         
                                        ]) !!}
                                        <span class="help-block m-b-none">{{ $errors->first('census') }}</span>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group @if (!empty($errors->first('card_id'))) has-error @endif">
                                    {!! Form::label('card_id', trans('app.身份证号码'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        {!! Form::text('card_id', isset($user->userExt->card_id) ? $user->userExt->card_id : old('card_id'), [
                                        'class' => 'form-control',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.身份证号码')]),
                                         
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
                                         
                                        ]) !!}
                                        <span class="help-block m-b-none">{{ $errors->first('card_address') }}</span>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group @if (!empty($errors->first('qq'))) has-error @endif">
                                    {!! Form::label('qq', trans('app.QQ号码'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        {!! Form::text('qq', isset($user->userExt->qq) ? $user->userExt->qq : old('qq'), [
                                        'class' => 'form-control',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.QQ号码')]),

                                        ]) !!}
                                        <span class="help-block m-b-none">{{ $errors->first('qq') }}</span>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group @if (!empty($errors->first('live_address'))) has-error @endif">
                                    {!! Form::label('live_address', trans('app.居住地址'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        {!! Form::text('live_address', isset($user->userExt->live_address) ? $user->userExt->live_address : old('live_address'), [
                                        'class' => 'form-control',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.居住地址')]),
                                         
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
                                         
                                        ]) !!}
                                        <span class="help-block m-b-none">{{ $errors->first('urgent_tel') }}</span>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group @if (!empty($errors->first('salary_card'))) has-error @endif">
                                    {!! Form::label('salary_card', trans('app.工资卡'), ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-6">
                                        {!! Form::text('salary_card', isset($user->userExt->salary_card) ? $user->userExt->salary_card : old('salary_card'), [
                                        'class' => 'form-control',
                                        'placeholder' => trans('app.请输入', ['value' => trans('app.工资卡')]),
                                         
                                        ]) !!}
                                        <span class="help-block m-b-none">{{ $errors->first('salary_card') }}</span>
                                    </div>
                                </div>


                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-6">
                                    {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                                </div>
                            </div>

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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



