<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 11:57
 * 调休假 计算类型
 */

namespace App\Components\AttendanceService\Cypher;

use App\Components\Helper\DataHelper;
use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;

class Change extends Cypher
{
    public function check($holidayConfig, $numberDay)
    {
        $leaveInfo = self::getUserHoliday(\Auth::user()->userExt->entry_time, \Auth::user()->user_id, $holidayConfig);

        if(empty($leaveInfo['data'][$numberDay]) || $leaveInfo['data'][$numberDay] <= 0 ) {
            return $this->backCypherData(false, ['start_time' => '剩余调休假次数不足']);
        }
        return $this->backCypherData(true);
    }

    public function getUserHoliday($entryTime, $userId, $holidayConfig)
    {
        $leaveInfo = $this->getUserPayableDayToNaturalCycleTime($entryTime, $userId, $holidayConfig)['userLeaveInfo'];

        $msgArr = $pointList = [];

        foreach (Leave::$workTimePoint as $k => $v) {
            $num = $leaveInfo[$k] ?? 0;
            if($num !== 0) {
                $pointList[] = ['id' => $k, 'text' => $v];
            }

            $msgArr[$k] = $v .' 剩余调休次数: ' . $num ;
        };

        $msg = '<i class="fa fa-info-circle"></i>调休剩余列表<br>' . implode('<br>', $msgArr);

        return [
            'status' => 1,
            'show_day' => true,
            'show_memo' => true,
            'memo' => $holidayConfig->memo,
            'number_day' => $leaveInfo,
            'count_num' => $leaveInfo,
            'holiday_name' => $holidayConfig->show_name,
            'data' => $leaveInfo,
            'point_list' => $pointList,
            'msg' => $msg
        ];
    }

    /*public function getDaysByScope($scope, $userId, $holidays)
    {
        return parent::getOverDaysByScope($scope, $userId, $holidays, HolidayConfig::CHANGE);
    }*/

    /**
     * 按自然周期时间维度获取带薪假期
     * @param $userId
     * @param $holiday
     * @return mixed
     */
    public function getUserPayableDayToNaturalCycleTime($entryTime, $userId, $holiday)
    {
        $resetDate = self::resolveCycleConfigFormula($holiday->work_reset_formula);

        $resetTime = sprintf('%s %s:%s:%s', $resetDate['d'] ??  '01', $resetDate['h'] ?? '00', $resetDate['i'] ?? '00', $resetDate['s'] ?? '00');
        $resetMoney = DataHelper::dateTimeFormat('now', 'Y') .'-'. DataHelper::dateTimeFormat('now', 'm') . '-' . $resetTime;

        if(array_key_exists('m', $resetDate)) {
            $startTime = DataHelper::dateTimeFormat('now', 'Y')  .'-'. $resetDate['m'] . '-' . $resetTime;
            $endTime = DataHelper::dateTimeAdd($startTime, '1Y');
        } elseif(empty($resetDate['m']) && !empty($resetDate['d'])) {
            $startTime = $resetMoney;
            $endTime = DataHelper::dateTimeAdd($startTime, '1M');
        } elseif(empty($resetDate['m']) && empty($resetDate['d']) && !empty($resetDate['h'])) {
            $startTime = $resetMoney;
            $endTime = DataHelper::dateTimeAdd($startTime, '1D');
        } else {
            $startTime = $resetMoney;
            $endTime = DataHelper::dateTimeAdd($startTime, 'T1H');
        }

        return $this->selectLeaveInfo($startTime, $endTime, $userId, $holiday);
    }

    /**
     * 查询员工 周期内 申请信息
     * @param $startDay
     * @param $endDay
     * @param $userId
     * @param $holiday
     * @return array number_day 申请天数 count_num申请次数
     */
    public function selectLeaveInfo($startDay, $endDay, $userId, $holiday)
    {
        $userLeaveInfo  = $overTimeLeave = $changeLeave = [];
        //加班类型ID
        $overTimeId = HolidayConfig::where(['cypher_type' => HolidayConfig::CYPHER_OVERTIME])->first();
        if (empty($overTimeId)) return $userLeaveInfo;
        //获取加班剩余次数
        $overTimeLeaveLog = self::selectLeave($startDay, $endDay, $userId, $overTimeId->holiday_id, [Leave::PASS_REVIEW]);
        //申请调休的申请单次数
        $changeLeaveLog = self::selectLeave($startDay, $endDay, $userId, $holiday->holiday_id, Leave::$statusList);

        if(!empty($overTimeLeaveLog)) {
            foreach ($overTimeLeaveLog as $lk => $lv) {
                $userLeaveInfo[(int)$lk] = $lv;
                $overTimeLeave[(int)$lk] = $lv;
            }
            if(!empty($changeLeaveLog)) {
                foreach ($changeLeaveLog as $ck => $cv) {
                    if(empty($userLeaveInfo[(int)$ck])) continue;
                        $userLeaveInfo[(int)$ck] = $userLeaveInfo[(int)$ck] - $cv;
                    $changeLeave[(int)$ck] = $cv;
                }
            }
        }
        //dd($overTimeId, $overTimeLeaveLog, $changeLeaveLog, $userLeaveInfo);
        //dd($userLeaveInfo);
        return ['userLeaveInfo' => $userLeaveInfo,
                'overTimeLeaveLog' => $overTimeLeave,
                'changeLeaveLog' => $changeLeave];
    }

    /**
     * 申请单信息查询
     * @param $startDay
     * @param $endDay
     * @param $userId
     * @param $holidayId
     * @param $status
     * @return array
     */
    public function selectLeave($startDay, $endDay, $userId, $holidayId, $status)
    {
        return Leave::select([
            \DB::raw('number_day'),
            \DB::raw('count(*) count_num'),
        ])
            ->where('start_time', '>=', $startDay)
            ->where('end_time', '<=', $endDay)
            ->whereIn('status', $status)
            ->where([
                'user_id' => $userId,
                'holiday_id' => $holidayId,
            ])
            ->groupBy(['user_id', 'number_day'])
            ->get(['number_day', 'count_num'])
            ->pluck('count_num', 'number_day')
            ->toArray();
    }

    /**
     * 获取申请天数
     * @param $params
     * @return int|number
     */
    public function getLeaveNumberDay($params)
    {

        $numberDay = 0;
        if(empty($params['startId'])) return $numberDay;

        $startId = Leave::$workTimePointChar[$params['startId']]['start_time'];
        $endId = Leave::$workTimePointChar[$params['startId']]['end_time'];
        $numberDay = DataHelper::leaveDayDiff($params['startTime'], $startId, $params['startTime'], $endId);

        return $numberDay;
    }
}