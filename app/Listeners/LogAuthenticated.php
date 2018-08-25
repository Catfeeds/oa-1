<?php

namespace App\Listeners;

use App\User;
use Illuminate\Auth\Events\Authenticated;

class LogAuthenticated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Authenticated  $event
     * @return void
     */
    public function handle(Authenticated $event)
    {
        //删除session，让其退出登陆状态
        if ($event->user->status == User::STATUS_DISABLE) {
            \Session::flush();
        }
    }
}
