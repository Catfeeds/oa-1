<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2017/11/18
 * Time: 14:31
 */
namespace App\Http\Components\BackstageApi;

class BackstageApiFacade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'backstageapi';
    }
}