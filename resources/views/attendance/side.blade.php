@if(Entrust::can(['leave*', 'staff*']))
    <li @if (Route::is(['leave*', 'daily-detail*', 'appeal*'])) class="active" @endif >
        <a href="#"><i class="fa fa-newspaper-o"></i> <span class="nav-label">{{ trans('att.考勤功能') }}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level">
            @if(Entrust::can(['leave', 'leave.edit', 'leave.create']))
                <li @if (Route::is(['leave.info', 'leave.optInfo', 'leave.edit', 'leave.create']) ) class="active" @endif>
                    <a href="{{ route('leave.info') }}">{{ trans('att.我的申请单') }}</a>
                </li>
            @endif
            @if(Entrust::can(['leave.review']))
                <li @if (Route::is(['leave.review.*']) ) class="active" @endif>
                    <a href="{{ route('leave.review.info') }}">{{ trans('att.申请单管理') }}</a>
                </li>
            @endif

            @if(Entrust::can(['daily-detail']))
                <li @if (Route::is(['daily-detail.info']) ) class="active" @endif>
                    <a href="{{ route('daily-detail.info') }}">{{ trans('att.我的考勤明细') }}</a>
                </li>
            @endif

            @if(Entrust::can(['daily-detail.*']))
                <li @if (Route::is(['daily-detail.review.*']) ) class="active" @endif>
                    <a href="{{ route('daily-detail.review.info') }}">{{ trans('att.考勤管理') }}</a>
                </li>
            @endif

            @if(Entrust::can(['appeal.review']))
                <li @if (Route::is(['appeal.review.*']) ) class="active" @endif>
                    <a href="{{ route('appeal.review.info') }}">{{ trans('att.申诉管理') }}</a>
                </li>
            @endif
        </ul>
    </li>
@endif

@if(Entrust::can(['staff*', 'entry*']))
    <li @if (Route::is(['staff*', 'entry*', 'manage*'])) class="active" @endif >
        <a href="#"><i class="fa fa-newspaper-o"></i> <span class="nav-label">{{ trans('staff.员工管理') }}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level">
            @if(Entrust::can(['manage.index']))
                <li @if (Route::is(['manage.index']) ) class="active" @endif>
                    <a href="{{ route('manage.index') }}">{{ trans('staff.员工工作台') }}</a>
                </li>
            @endif

            @if(Entrust::can(['staff*']))
                <li @if (Route::is(['staff*']) ) class="active" @endif>
                    <a href="{{ route('staff.list') }}">{{ trans('staff.员工列表') }}</a>
                </li>
            @endif

            @if(Entrust::can(['entry*']))
                <li @if (Route::is(['entry*']) ) class="active" @endif>
                    <a href="{{ route('entry.list') }}">{{ trans('staff.员工入职') }}</a>
                </li>
            @endif

        </ul>
    </li>
@endif

@if(Entrust::can(['holiday-config', 'approval-step', 'punch-rules', 'calendar', 'dept',
                'job', 'school', 'firm', 'bulletin']))
    <li @if (Route::is(['holiday-config*', 'approval-step*', 'punch-rules*', 'calendar*', 'dept*',
                'job*', 'school*', 'firm*', 'bulletin*', 'inventory*'])) class="active" @endif >
        <a href="#"><i class="fa fa-newspaper-o"></i> <span class="nav-label">{{ trans('staff.系统配置') }}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level">

            <li @if (Route::is(['holiday-config*', 'approval-step*', 'punch-rules*', 'calendar*']) ) class="active" @endif>
                <a href="{{ route('holiday-config') }}">{{ trans('staff.考勤信息配置') }}</a>
            </li>

            <li @if (Route::is(['dept*', 'job*', 'school*', 'firm*']) ) class="active" @endif>
                <a href="{{ route('dept') }}">{{ trans('staff.员工信息配置') }}</a>
            </li>

            @if(Entrust::can('bulletin.index'))
                <li @if (Route::is(['bulletin*']) ) class="active" @endif>
                    <a href="{{ route('bulletin.index') }}">{{ trans('app.公告栏信息配置') }}</a>
                </li>
            @endif

            @if(Entrust::can('material.inventory-list'))
                <li @if(Route::is('inventory*')) class="active" @endif>
                    <a href="{{ route('inventory.list') }}">{{ trans('material.资质外借库存配置') }}</a>
                </li>
            @endif
        </ul>
    </li>
@endif

@if(Entrust::can(['material.approve*', 'material.apply*']))
    <li @if(Route::is('material*')) class="active" @endif>
        <a href="#"><i class="fa fa-newspaper-o"></i> <span class="nav-label">{{ trans('material.物料管理系统') }}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level">
            @if(Entrust::can(['material.apply.index']))
                <li @if (Route::is(['material.apply*']) ) class="active" @endif>
                    <a href="{{ route('material.apply.index') }}">{{ trans('material.资质外借') }}</a>
                </li>
            @endif

            @if(Entrust::can(['material.approve.index']))
                <li @if (Route::is(['material.approve*']) ) class="active" @endif>
                    <a href="{{ route('material.approve.index', ['state' => 'all']) }}">{{ trans('material.资质外借审批') }}</a>
                </li>
            @endif

            {{--@if(Entrust::can(['entry*']))--}}
            <li @if (Route::is(['entry*']) ) class="active" @endif>
                <a href="#">{{ trans('material.会议室租用管理') }}</a>
            </li>
            {{--@endif--}}
        </ul>
    </li>
@endif


