<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $user;

    public function callAction($method, $parameters)
    {
        $this->init();
        return parent::callAction($method, $parameters);
    }

    public function init()
    {
        activity()
            ->useLog('action')
            ->withProperties(\Request::all())
            ->log(\Route::current()->getActionName());
    }

    protected function getUser()
    {
        $this->user = \Auth::user();
        return $this->user;
    }

}
