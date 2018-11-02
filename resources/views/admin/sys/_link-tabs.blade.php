@if(Entrust::can(['holiday-config']))
    <li @if (Route::is('holiday-config*')) class="active" @endif>
        <a href="{{ route('holiday-config') }}">{{ trans('app.申请类型配置') }}</a>
    </li>
@endif

@if(Entrust::can(['approval-step']))
    <li @if (Route::is('review-step-flow*')) class="active" @endif>
        <a href="{{ route('review-step-flow') }}">{{ trans('app.审核流程配置') }}</a>
    </li>
@endif

@if(Entrust::can(['punch-rules']))
    <li @if (Route::is('punch-rules*')) class="active" @endif>
        <a href="{{ route('punch-rules') }}">{{ trans('app.上下班时间规则配置') }}</a>
    </li>
@endif

@if(Entrust::can(['calendar']))
    <li @if (Route::is('calendar*')) class="active" @endif>
        <a href="{{ route('calendar') }}">{{ trans('app.日历表配置') }}</a>
    </li>
@endif

