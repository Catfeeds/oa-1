<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/13
 * Time: 21:06
 */

namespace App\Http\Components\Helpers;


use App\Models\Sys\OperateLog;

class OperateLogHelper
{
    const LEAVE_TYPE_ID = 1;
    /**
     * 操作日志
     * @param int $typeId 操作类型ID 1:请假模块
     * @param int $infoId 操作记录ID
     * @param string $opt_name 操作内容
     * @param string $memo 备注
     */
    public static function createOperateLog($typeId, $infoId, $opt_name, $memo = '')
    {
        $log = [
            'type_id' => $typeId,
            'info_id' => $infoId,
            'opt_uid' => \Auth::user()->user_id,
            'opt_name' => $opt_name,
            'memo' => $memo,
        ];

        if($infoId > 0) OperateLog::create($log);
    }

}