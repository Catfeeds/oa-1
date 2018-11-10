<?php
/**
 * Created by PhpStorm.
 * User: wangyingjie
 * Date: 2018/10/16
 * Time: 14:30
 */
namespace App\Http\Components\Helpers;
use App\Components\Helper\DataHelper;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\Calendar;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\PunchRulesConfig;

class ReviewHelper
{
    public $yearName = '年假';
    public $visitName = '探亲假';

    /**
     * @return array
     */
    public function ifConfig(): array
    {
        $message = [];
        if (!$yearHolObj = HolidayConfig::getObjByName($this->yearName)) {
            $message['年假'] = ['message' => "请添加或修改假期配置名称成: '$this->yearName'后再进行", 'sign' => 'danger'];
        }
        if (!$visitHolObj = HolidayConfig::getObjByName($this->visitName)) {
            $message['探亲'] = ['message' => "请添加或修改假期配置名称成: '$this->visitName'后再进行", 'sign' => 'danger'];
        }
        return [$message, $yearHolObj, $visitHolObj];
    }
    /**
     * 有权限则跳转到假期配置页,没有则在页面判断,显示联系管理员
     * @param $monthInfo
     * @return bool
     */
    public function errorRedirect($monthInfo)
    {
        if ($monthInfo[0] == 'error' && \Entrust::can(['holiday-config', 'holiday-config-all'])) {
            foreach ($monthInfo[1] as $message) {
                flash($message['message'], $message['sign']);
            }
            return true;
        }
        return false;
    }

    //是否全勤:应到天数等于实到 无影响全勤 迟到分钟数合计为0
    public function ifPresentAllDay($shouldCome, $actuallyCome, $affectFull, $user, $beLateNum)
    {
        $isFullWork = ($shouldCome <= $actuallyCome &&
            !isset($affectFull[$user->user_id]) &&
            ($beLateNum[$user->user_id] ?? '') === '0') ? '是' : '否';
        return $isFullWork;
    }

    /**
     * 计算各种带薪假的剩余天数
     * @param $user
     * @param array $obj
     * @return array
     */
    public function countWelfare($user, array $obj)
    {
        $ret = [];
        $arr = ['et' => $user->userExt->entry_time, 'id' => $user->user_id];
        foreach ($obj as $k => $v) {
            if(empty($v->cypher_type)) continue;
            $driver = HolidayConfig::$cypherTypeChar[$v->cypher_type];
            $ret[$k] = \AttendanceService::driver($driver, 'cypher')->getUserHoliday($arr['et'], $arr['id'], $v);
        }
        return $ret;
    }
    /*public function countWelfare($user, $obj)
    {
        $ret = [];
        $arr = ['et' => $user->userExt->entry_time, 'id' => $user->user_id];
        foreach ($obj as $k => $v) {
            if(empty($v->cypher_type)) continue;
            $driver = HolidayConfig::$cypherTypeChar[$v->cypher_type];
            $re = \AttendanceService::driver($driver, 'cypher')->getUserHoliday($arr['et'], $arr['id'], $v);
            $ret[$re['holiday_id']] = $re;
        }
        return $ret;
    }*/

    public function getHolidayConfigByCypherTypes(array $cypherTypes)
    {
        $holCon = [];
        foreach ($cypherTypes as $cypherType) {
            $holCon[$cypherType] = HolidayConfig::where('cypher_type', $cypherType)->get();
        }
        return $holCon;
    }

    public function countActuallyDays($startDate, $endDate, $user)
    {
        $dailies = DailyDetail::whereBetween('day', [$startDate, $endDate])->where('user_id', $user->user_id)->count();
        $overDays = Leave::whereBetween('end_time', [$startDate, $endDate])->where('user_id', $user->user_id)
            ->whereIn('status', [Leave::PASS_REVIEW, Leave::SWITCH_REVIEW_ON])
            ->whereHas('holidayConfig', function ($query) {
                $query->where('cypher_type', HolidayConfig::CYPHER_OVERTIME);
            })->count();

        $leaves = Leave::whereBetween('end_time', [$startDate, $endDate])->where('user_id', $user->user_id)
            ->whereIn('status', [Leave::PASS_REVIEW, Leave::SWITCH_REVIEW_ON])
            ->whereHas('holidayConfig', function ($query) {
                $query->whereNotIn('cypher_type', [HolidayConfig::CYPHER_OVERTIME]);
            })
            ->sum('number_day');
        return $dailies - $overDays - $leaves;
    }


    /**
     * 获取上下班时间早退或迟到时的标记,用于在前端显示红色标明
     * @param $startDate
     * @param $endDate
     * @param array $dailyDetailData 打卡明细数组
     * @return array e.g: $danger['2018-10-10']['on_work' => true, 'off_work' => false]
     */
    public function getDanger($startDate, $endDate, $dailyDetailData)
    {
        $punchHelper = app(PunchHelper::class);
        $danger = array();
        $calPunch = $punchHelper->getCalendarPunchRules($startDate, $endDate);
        foreach ($dailyDetailData as $datum) {
            $danger[$datum->day] = $punchHelper->getDeduct($datum->punch_start_time, $datum->punch_end_time, $calPunch[$datum->day])['danger'];
            $leaveArr = json_decode($datum->leave_id, true);
            if (!empty($leaveArr)) {
                //这天若打卡在请假区间,false不显示红色
                $leaves = Leave::whereIn('leave_id', $leaveArr)->with('holidayConfig')->get();
                foreach ($leaves as $leaf) {
                    if ($leaf->holidayConfig->cypher_type != HolidayConfig::CYPHER_SWITCH) {
                        $leafStart = strtotime(date('Y-m-d', strtotime($leaf->start_time)).' '.$leaf->start_id);
                        $leafEnd = strtotime(date('Y-m-d', strtotime($leaf->end_time)).' '.$leaf->end_id);
                        $datumStart = strtotime($datum->day.' '.$datum->punch_start_time);
                        $datumEnd = strtotime($datum->day.' '.$datum->punch_end_time);

                        if (DataHelper::ifBetween($leafStart, $leafEnd, $datumStart)) {
                            $danger[$datum->day]['on_work'] = false; break;
                        }
                        if (DataHelper::ifBetween($leafStart, $leafEnd, $datumEnd)) {
                            $danger[$datum->day]['off_work'] = false; break;
                        }
                    }
                }
            }
        }
        return $danger;
    }

}