@if(Entrust::can(['dept']))
    <li @if (Route::is('dept*')) class="active" @endif>
        <a href="{{ route('dept') }}">{{ trans('app.部门') }}</a>
    </li>
@endif

@if(Entrust::can(['job']))
    <li @if (Route::is('job*')) class="active" @endif>
        <a href="{{ route('job') }}">{{ trans('app.岗位') }}</a>
    </li>
@endif

@if(Entrust::can(['school']))
    <li @if (Route::is('school*')) class="active" @endif>
        <a href="{{ route('school') }}">{{ trans('app.学校') }}</a>
    </li>
@endif

@if(Entrust::can(['firm']))
    <li @if (Route::is(['firm*']) ) class="active" @endif>
        <a href="{{ route('firm') }}">{{ trans('staff.公司配置') }}</a>
    </li>
@endif