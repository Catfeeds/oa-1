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
                        {!! Form::label('name', trans('staff.姓名'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('name', $entry->name  ,['class' => 'form-control', 'disabled']) !!}
                        </div>

                        {!! Form::label('dept_id', trans('staff.所属部门'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('dept_id', $dept[$entry->dept_id] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('sex', trans('staff.性别'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('sex', \App\Models\UserExt::$sex[$entry->sex] ?? ''  ,['class' => 'form-control', 'disabled']) !!}
                        </div>

                        {!! Form::label('job_name', trans('staff.岗位名称'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('job_name', $entry->job_name ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('mobile', trans('staff.手机号码'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('mobile', $entry->mobile ,['class' => 'form-control', 'disabled']) !!}
                        </div>

                        {!! Form::label('email', trans('staff.邮箱'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('email', $entry->email ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>
                    {{--岗位信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.岗位信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group ">
                        {!! Form::label('entry_time', trans('app.入职时间'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('entry_time', $entry->entry_time ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('nature_id', trans('staff.工作性质'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('nature_id', \App\Models\StaffManage\Entry::$nature[$entry->nature_id] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('hire_id', '招聘类型', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('hire_id', \App\Models\StaffManage\Entry::$hireTYpe[$entry->hire_id] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('firm_id', trans('staff.所属公司'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('firm_id', $firm[$entry->firm_id] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('job_name', trans('staff.岗位类型'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('job_id', $job[$entry->job_id] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('job_name', trans('staff.岗位名称'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('job_name', $entry->job_name ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    {{--关系信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.关系信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('leader_id', trans('staff.直属上级'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('leader_id', $users[$entry->leader_id] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('tutor_id', trans('staff.导师'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('tutor_id', $users[$entry->tutor_id] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('friend_id', trans('staff.基友'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('friend_id', $users[$entry->friend_id] ?? '' ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('place', trans('staff.工作位置'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('place', $entry->place ?? '' ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('copy_users', trans('staff.抄送人员'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            <select disabled  multiple="multiple" name="copy_users[]" id="copy_users" class="js-select2-multiple form-control">
                                @foreach($users as $key => $val)
                                    <option value="{{ $key }}"
                                            @if (in_array($key, $userIds ?: old('copy_users') ?? [])) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{--基本信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.基本信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('card_id', trans('staff.身份证号码'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('card_id', $entry->card_id  ,['class' => 'form-control', 'disabled']) !!}
                        </div>

                    </div>

                    <div class="form-group">
                        {!! Form::label('card_address', trans('staff.身份证地址'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('card_address', $entry->card_address  ,['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('ethnic', trans('staff.民族'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('ethnic', $entry->ethnic, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('birthplace', trans('staff.籍贯'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('birthplace', $entry->birthplace, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('political', trans('staff.政治面貌'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('political', $entry->political, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('census', trans('staff.户籍类型'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('census', $entry->census, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('marital_status', '婚姻状况', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('marital_status', \App\Models\UserExt::$marital[$entry->marital_status] ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('blood_type', trans('staff.血型'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('blood_type', \App\Models\UserExt::$blood[$entry->blood_type] ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('genus_id', trans('staff.属相'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('genus_id', \App\Models\UserExt::$genus[$entry->genus_id] ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('constellation_id', trans('staff.星座'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('constellation_id', \App\Models\UserExt::$constellation[$entry->constellation_id] ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('dept', trans('staff.身高'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('height', $entry->height ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('weight', trans('staff.体重'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('weight', $entry->weight ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    {{--联系信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.联系信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('qq', trans('staff.QQ号码'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('qq', $entry->qq ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('live_address', trans('staff.目前住址'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('live_address', $entry->live_address ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('family_num', trans('staff.家庭成员人数'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('family_num', $entry->family_num ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('urgent_name', trans('staff.紧急联系人姓名'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('urgent_name', $entry->urgent_name ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('urgent_bind', trans('staff.与紧急联系人的关系'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('urgent_bind', $entry->urgent_bind ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('urgent_tel', trans('staff.紧急联系人电话'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('urgent_tel', $entry->urgent_tel ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    {{--学历信息--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-navy"><h2>{{trans('staff.学历信息')}}</h2</label>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('education_id', trans('staff.最高学历'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('education_id', \App\Models\UserExt::$education[$entry->education_id] ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('school_id', trans('staff.毕业学校'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('school_id', $school[$entry->school_id] ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('graduation_time', trans('staff.毕业时间'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('graduation_time', $entry->graduation_time ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('specialty', trans('staff.专业'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('specialty', $entry->specialty ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('degree', trans('staff.学位'), ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('degree', $entry->degree ?? '', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-5">
                            @if(Entrust::can(['entry.review']) && in_array($entry->status, [\App\Models\StaffManage\Entry::FILL_END]))
                                <a id="pass" class="btn btn-success">{{ trans('staff.确认入职') }}</a>
                            @endif
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
            $('#copy_users').select2();

            $('#pass').click(function () {
                if(confirm('确认办理入职')==false) return false;

                $.get('{{ route('entry.pass', ['id' => $entry->entry_id])}}', function ($data) {
                    if ($data.status == 1) {
                        bootbox.alert($data.msg);
                        location.reload();
                    } else {
                        bootbox.alert($data.msg);
                    }
                })
            });

        });
    </script>
@endsection