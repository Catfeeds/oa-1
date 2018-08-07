<div class="panel-options">
    <ul class="nav nav-tabs">
        <li @if (Route::is('profile')) class="active" @endif>
            <a href="{{ route('profile') }}">{{ trans('app.个人信息') }}</a>
        </li>
        <li @if (Route::is(['profile.edit', 'profile.confirmEdit'])) class="active" @endif>
            @if(\App\Models\UserExt::checkIsConfirm(Auth::user()->user_id)[0]->is_confirm == 0)
                <a href="{{ route('profile.confirmEdit') }}">{{ trans('app.设置') }}</a>
            @else
                <a href="{{ route('profile.edit') }}">{{ trans('app.设置') }}</a>
            @endif
        </li>
        @if(Entrust::can(['profile.password']))
            <li @if (Route::is('profile.reset-password')) class="active" @endif>
                <a href="{{ route('profile.reset-password') }}">{{ trans('app.重置密码') }}</a>
            </li>
        @endif
    </ul>
</div>
