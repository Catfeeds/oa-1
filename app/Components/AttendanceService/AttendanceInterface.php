<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/4
 * Time: 10:13
 * 考勤抽象类
 */

namespace App\Components\AttendanceService;


interface AttendanceInterface
{
    /**
     * 申请单检验和数据返回
     * @param $request
     * @return array
     */
    public function checkLeave($request) : array;

    /**
     * 写入申请单信息
     * @param array $leave
     * @return array
     */
    public function createLeave(array $leave) : array;

    /**
     * 获取审核步骤
     * @param $request
     * @param $numberDay
     * @return array
     */
    public function getLeaveStep($request, $numberDay) : array;

}