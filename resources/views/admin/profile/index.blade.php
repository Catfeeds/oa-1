@extends('admin.profile.profile')

@section('content-profile')
    <div class="col-sm-6 b-r">
        <p><i class="fa fa-user"></i> {{ trans('app.账号') }} ：<strong>{{ $user->username }}</strong></p>
        <p><i class="fa fa-tag"></i> {{ trans('app.姓名') }} ：{{ $user->alias }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.部门') }} ：{{ isset($user->dept_id) ?  $dept[$user->dept_id] : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.岗位') }} ：{{  $job[$user->job_id] ?? ''  }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.邮箱') . '： ' . $user->email }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.性别') }} ：{{ isset($userExt->userExt->sex) ?  \App\Models\UserExt::$sex[$userExt->userExt->sex] : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.年龄') }} ：{{ isset($userExt->userExt->age) ? $userExt->userExt->age : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.学历') }} ：{{ empty($userExt->userExt->education_id) ? '' : \App\Models\UserExt::$education[$userExt->userExt->education_id]  }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.血型') }} ：{{ empty($userExt->userExt->blood_type) ? '' : \App\Models\UserExt::$blood[$userExt->userExt->blood_type] }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.星座') }} ：{{ isset($userExt->userExt->constellation_id) ? \App\Models\UserExt::$constellation[$userExt->userExt->constellation_id] : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.婚姻状况') }} ：{{ isset($userExt->userExt->marital_status) ? \App\Models\UserExt::$marital[$userExt->userExt->marital_status] : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.毕业学校') }} ：{{ isset($userExt->userExt->school) ? $school[$userExt->userExt->school] : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.毕业时间') }} ：{{ isset($userExt->userExt->graduation_time) ? $userExt->userExt->graduation_time : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.入职时间') }} ：{{ isset($userExt->userExt->entry_time) ? $userExt->userExt->entry_time : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.转正时间') }} ：{{ isset($userExt->userExt->turn_time) ? $userExt->userExt->turn_time : '' }}</p>
    </div>
    <div class="col-sm-6">
        <p><i class="fa fa-tag"></i> {{ trans('app.合同开始时间') }} ：{{ isset($userExt->userExt->contract_st) ? $userExt->userExt->contract_st : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.合同结束时间') }} ：{{ isset($userExt->userExt->contract_et) ? $userExt->userExt->contract_et : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.合同年限') }} ：{{ isset($userExt->userExt->contract_years) ? $userExt->userExt->contract_years : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.合同签约次数') }} ：{{ isset($userExt->userExt->contract_num) ? $userExt->userExt->contract_num : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.在职年数') }} ：{{ isset($userExt->userExt->incumbent_num) ? $userExt->userExt->incumbent_num : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.籍贯') }} ：{{ isset($userExt->userExt->birthplace) ? $userExt->userExt->birthplace : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.家庭成员人数') }} ：{{ isset($userExt->userExt->family_num) ? $userExt->userExt->family_num : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.户籍类型') }} ：{{ isset($userExt->userExt->census) ? $userExt->userExt->census : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.身份证号码') }} ：{{ isset($userExt->userExt->card_id) ? $userExt->userExt->card_id : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.身份证地址') }} ：{{ isset($userExt->userExt->card_address) ? $userExt->userExt->card_addres : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.QQ号码') }} ：{{ isset($userExt->userExt->qq) ? $userExt->userExt->qq : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.居住地址') }} ：{{ isset($userExt->userExt->live_address) ? $userExt->userExt->live_address : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.紧急联系人姓名') }} ：{{ isset($userExt->userExt->urgent_name) ? $userExt->userExt->urgent_name : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.紧急联系人电话') }} ：{{ isset($userExt->userExt->urgent_tel) ? $userExt->userExt->urgent_tel : '' }}</p>
        <p><i class="fa fa-tag"></i> {{ trans('app.工资卡') }} ：{{ isset($userExt->userExt->salary_card) ? $userExt->userExt->salary_card : '' }}</p>
    </div>
@endsection
