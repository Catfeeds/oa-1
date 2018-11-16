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
    const MATERIAL = 5;
    /**
     * 操作日志
     * @param int $typeId 操作类型ID 1:请假模块 5:物料模块
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

    /**
     * 通过员工ID获取审核过的请假单
     * @param $userId
     * @return array
     */
    public static function getLogInfoIdToUid($userId, $typeId = OperateLogHelper::LEAVE_TYPE_ID)
    {
        $infoIds = OperateLog::where(['opt_uid' => $userId])
            ->where('type_id', $typeId)
            ->whereIn('opt_name', ['审核通过', '拒绝通过'])
            ->get(['info_id'])
            ->pluck('info_id')
            ->toArray();

        return array_unique($infoIds);
    }

    /**
     * 通过信息ID获取员工ID
     * @param $infoId
     * @return array
     */
    public static function getLogUserIdToInId($infoId)
    {
        $userIds = OperateLog::where(['info_id' => $infoId])
            ->get(['opt_uid'])
            ->pluck('opt_uid')
            ->toArray();
        return $userIds;
    }

    /**
     * 微信 信息推送接口
     * @param $userId //工号ID 支持多个 |分割 sy0001|sy0002
     * @param $message //消息内容 支持a标签和换行
     */
    public static function sendWXMsg($userId, $message)
    {
        $time = time();
        $pushData = [
            'userid'  => $userId,
            'message' => $message,
            'dateTime' => $time,
            'sign' => md5($userId . 'oU0lD8GRVpvYfYUq6ensuQtHUkwtE0o3' . $time)
        ];
        //微信通知推送消息接口
        \BackstageApi::sendWXMsg($pushData, 'push');
    }
}