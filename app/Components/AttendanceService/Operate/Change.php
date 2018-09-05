<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/3
 * Time: 19:53
 * 申请调休
 */
namespace App\Components\AttendanceService\Operate;

use App\Components\AttendanceService\AttendanceInterface;

class Change extends Operate implements AttendanceInterface
{
    public function checkLeave($request): array
    {
        return parent::checkLeave($request);
    }

    public function createLeave(array $leave): array
    {
        return parent::createLeave($leave);
    }
}