@if(Entrust::can(['attendance-all', 'leave-all']))
    <li @if (Route::is('leave*')) class="active" @endif >
        <a href="#"><i class="fa fa-newspaper-o"></i> <span class="nav-label">{{ trans('att.考勤管理') }}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level">
            @if(Entrust::can(['leave-all', 'attendance-all']))
                <li @if (Route::is('leave*') ) class="active" @endif>
                    <a href="{{ route('leave.info') }}">{{ trans('att.我的假期') }}</a>
                </li>
            @endif
        </ul>
    </li>
@endif