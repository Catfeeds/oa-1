<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/16
 * Time: 10:30
 * 操作状态类
 */

namespace App\Components\AttendanceService\OptStatus;


use App\Http\Components\Helpers\OperateLogHelper;

class Opt
{
    /**
     * 操作日志记录
     * @param int $typeId 记录操作类型APP_ID
     * @param int $infoId 操作ID
     * @param string $msg 操作信息
     */
    public function createOptLog($typeId, $infoId, $msg)
    {
        OperateLogHelper::createOperateLog($typeId, $infoId, $msg);
    }

}