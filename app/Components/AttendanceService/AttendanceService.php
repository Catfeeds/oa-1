<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/3
 * Time: 19:41
 * 考勤接口工厂类
 * 调用案例：调用请假
 * $ret = \GameApi::driver('leave')->createLeave(参数);
 */
namespace App\Components\AttendanceService;

class AttendanceService
{
    /**
     * 接口驱动 php
     * 文件首字母为大写，后面统一为小写
     * @param string $driver 驱动类型
     * @return // Operate\Leave
     */
    public function driver(string $driver, $file = 'operate')
    {
        $file = ucfirst(strtolower($file));
        $driver = ucfirst(strtolower($driver));

        $className = __NAMESPACE__ . "\\" . $file . "\\" . $driver;
        return new $className();
    }
}