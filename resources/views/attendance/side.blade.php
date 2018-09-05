@if(Entrust::can(['leave*', 'attendance-all', 'daily-detail-all']))
    <li @if (Route::is(['leave*', 'daily-detail*'])) class="active" @endif >
        <a href="#"><i class="fa fa-newspaper-o"></i> <span class="nav-label">{{ trans('att.考勤功能') }}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level">
            @if(Entrust::can(['attendance-all', 'leave-all', 'leave', 'leave.edit', 'leave.create']))
                <li @if (Route::is(['leave.info', 'leave.optInfo', 'leave.edit', 'leave.create']) ) class="active" @endif>
                    <a href="{{ route('leave.info') }}">{{ trans('att.我的假期') }}</a>
                </li>
            @endif
            @if(Entrust::can(['attendance-all', 'leave-all', 'leave.review']))
                <li @if (Route::is(['leave.review.*']) ) class="active" @endif>
                    <a href="{{ route('leave.review.info') }}">{{ trans('att.申请单管理') }}</a>
                </li>
            @endif

            @if(Entrust::can(['attendance-all', 'daily-detail-all', 'daily-detail']))
                <li @if (Route::is(['daily-detail.info']) ) class="active" @endif>
                    <a href="{{ route('daily-detail.info') }}">{{ trans('att.我的考勤明细') }}</a>
                </li>
            @endif

            @if(Entrust::can(['attendance-all', 'daily-detail.*']))
                <li @if (Route::is(['daily-detail.review.*']) ) class="active" @endif>
                    <a href="{{ route('daily-detail.review.info') }}">{{ trans('att.考勤管理') }}</a>
                </li>
            @endif
        </ul>
    </li>
@endif