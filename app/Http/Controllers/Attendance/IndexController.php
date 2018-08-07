<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 10:58
 */
namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $title = '考勤系统首页';
        return view('attendance.index', compact('title'));
    }
}
