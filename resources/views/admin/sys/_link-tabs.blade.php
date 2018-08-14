@if(Entrust::can(['dept-all', 'dept']))
    <li @if (Route::is('dept*')) class="active" @endif>
        <a href="{{ route('dept') }}">{{ trans('app.部门') }}</a>
    </li>
@endif

@if(Entrust::can(['job-all', 'job']))
    <li @if (Route::is('job*')) class="active" @endif>
        <a href="{{ route('job') }}">{{ trans('app.岗位') }}</a>
    </li>
@endif

@if(Entrust::can(['school-all', 'school']))
    <li @if (Route::is('school*')) class="active" @endif>
        <a href="{{ route('school') }}">{{ trans('app.学校') }}</a>
    </li>
@endif

@if(Entrust::can(['holiday-config-all', 'holiday-config']))
    <li @if (Route::is('holiday-config*')) class="active" @endif>
        <a href="{{ route('holiday-config') }}">{{ trans('app.假期配置') }}</a>
    </li>
@endif

@if(Entrust::can(['approval-step-all', 'approval-step']))
    <li @if (Route::is('approval-step*')) class="active" @endif>
        <a href="{{ route('approval-step') }}">{{ trans('app.审核流程配置') }}</a>
    </li>
@endif

