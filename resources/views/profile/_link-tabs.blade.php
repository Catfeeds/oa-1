<div class="panel-options">
    <ul class="nav nav-tabs">
        <li @if (Route::is('profile')) class="active" @endif>
            <a href="{{ url('profile') }}">{{ trans('app.属性') }}</a>
        </li>
        <li @if (Route::is('profile.edit')) class="active" @endif>
            <a href="{{ url('profile/edit') }}">{{ trans('app.设置') }}</a>
        </li>
        @if(Entrust::can(['profile.password']))
            <li @if (Route::is('profile.reset-password')) class="active" @endif>
                <a href="{{ url('profile/reset-password') }}">{{ trans('app.重置密码') }}</a>
            </li>
        @endif
    </ul>
</div>
