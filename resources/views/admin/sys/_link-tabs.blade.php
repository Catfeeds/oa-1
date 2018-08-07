@if(Entrust::can(['attendance-all', 'attendance']))
    <li @if (Route::is('dept*')) class="active" @endif>
        <a href="{{ route('dept') }}">{{ trans('app.部门') }}</a>
    </li>
@endif
@if(Entrust::can(['attendance-all', 'attendance']))
    <li @if (Route::is('job*')) class="active" @endif>
        <a href="{{ route('job') }}">{{ trans('app.岗位') }}</a>
    </li>
@endif
@if(Entrust::can(['attendance-all', 'attendance']))
    <li @if (Route::is('school*')) class="active" @endif>
        <a href="{{ route('school') }}">{{ trans('app.学校') }}</a>
    </li>
@endif
{{--
@if(Entrust::can(['attendance-all', 'attendance']))
    <li @if (Route::is('job*')) class="active" @endif>
        <a href="{{ route('job') }}">{{ trans('app.假期配置') }}</a>
    </li>
@endif--}}
