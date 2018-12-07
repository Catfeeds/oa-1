<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 12:04
 * 补打卡 计算类型
 */

namespace App\Components\AttendanceService\Cypher;

use App\Components\Helper\DataHelper;
use App\Http\Components\Helpers\OperateLogHelper;

class Recheck extends Cypher
{
    public function check($holidayConfig, $numberDay)
    {
        return parent::check($holidayConfig, $numberDay);
    }

    public function getUserHoliday($entryTime, $userId, $holidayConfig)
    {
        return parent::getUserHoliday($entryTime, $userId, $holidayConfig);
    }

    /**
     * 获取申请天数
     * @param $params
     * @return int|number
     */
    public function getLeaveNumberDay($params)
    {
        $numberDay = 0;
        return $numberDay;
    }

    public function spliceLeaveTime($params)
    {

        return [
            'time'=> $params['time'],
            'number_day' => $params['timeId'] . '补卡'
        ];
    }

    /**
     * 微信消息内容
     * @param $msgArr
     */
    public function sendWXContent($msgArr)
    {
        $content =  '【'.$msgArr['applyType'].'】'.$msgArr['notice'].'
申请人：'.$msgArr['username'].'
所属部门：'.$msgArr['dept'].'
申请事项：'.$msgArr['holiday'].'
打卡时间：'.$msgArr['start_time'].' 
点击此处查看申请详情[<a href = "'.$msgArr['url'].'">点我前往</a>]';

        //企业微信通知审核人员
        OperateLogHelper::sendWXMsg($msgArr['send_user'], $content);
    }
}