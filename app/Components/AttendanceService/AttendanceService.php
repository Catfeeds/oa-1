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
     * 接口驱动 php Java C等
     * 文件首字母为大写，后面统一为小写
     * @param string $driver 驱动类型
     * @return // Operate\Leave
     */
    public function driver(string $driver)
    {
        //获取游戏后端语言，目前只支持erl，后期考虑动态支持其他语言
        $lang = ucfirst(strtolower('operate'));
        $driver = ucfirst(strtolower($driver));

        $className = __NAMESPACE__ . "\\" . $lang . "\\" . $driver;
        return new $className();
    }
}