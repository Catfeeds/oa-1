<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/4
 * Time: 10:20
 * 考勤操作通用方法类
 */

namespace App\Components\AttendanceService\Operate;

use App\Components\Helper\DataHelper;
use App\Models\Attendance\DailyDetail;
use App\Models\Sys\ApprovalStep;
use App\Models\Sys\ReviewStepFlow;
use App\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\Attendance\Leave;

class Operate
{
    use  ValidatesRequests;

    public $_validateRule = [
        'holiday_id' => 'required',
        'start_time' => 'required',
        'end_time' => 'required',
        'reason' => 'required',
    ];

    public $_validateRuleRe = [
        'holiday_id' => 'required',
        'annex' => 'required|file',
        'reason' => 'required',
    ];

    /**
     * 获取员工审核步骤
     * @param $numberDay //申请单天数
     * @return array
     */
    public function getLeaveStep($holidayId, $numberDay) : array
    {
        $steps = ReviewStepFlow::with('config')->where(['child_id' => $holidayId])->get()->toArray();

        $step = [];
        foreach ($steps as $sk => $sv) {
            if($sv['min_num'] <= $numberDay && $sv['max_num'] >= $numberDay) {
                $step = $sv;
                break;
            }
        }

        if(empty($step['config'])) return self::backLeaveData(false, ['holiday_id' => trans('申请失败, 未设置部门审核人员，有疑问请联系人事')]);

        $leaderStepUid = [];
        foreach ($step['config'] as $lk => $lv) {

            if((int)$lv['assign_type'] === 0) {
                $leaderStepUid[$lv['step_order_id']] = $lv['assign_uid'];
            }

            if((int)$lv['assign_type'] === 1) {
                $roleId = sprintf('JSON_EXTRACT(role_id, "$.id_%d") = "%d"', $lv['assign_role_id'], $lv['assign_role_id']);
                $userLeader = User::whereRaw('dept_id ='.\Auth::user()->dept_id .' and ' . $roleId )->first();
                if(empty($userLeader->user_id)) return self::backLeaveData(false, ['holiday_id' => trans('申请失败, 未设置部门主管权限，有疑问请联系人事')]);
                $leaderStepUid[$lv['step_order_id']] = $userLeader->user_id;
            }

        }
        ksort($leaderStepUid);
        $reviewUserId = reset($leaderStepUid);
        array_shift($leaderStepUid);
        if(empty($leaderStepUid)) {
            $remainUser = '';
        } else {
            $remainUser = json_encode($leaderStepUid);
        }

        $data = [
            'step_id' => $step['step_id'],
            'remain_user'  => $remainUser,
            'review_user_id' => $reviewUserId,
        ];

        return self::backLeaveData(true, [], $data);
    }

    /**
     * 创建申请单
     * @param array $leave
     */
    public function createLeave(array $leave) : array
    {
        $data = [
            'user_id' => \Auth::user()->user_id,
            'holiday_id' => $leave['holiday_id'],
            'step_id' => $leave['step_id'],
            'start_time' => $leave['start_time'],
            'start_id' => $leave['start_id'],
            'end_time' => $leave['end_time'],
            'end_id' => $leave['end_id'],
            'number_day' => $leave['number_day'],
            'reason' => $leave['reason'],
            'user_list' => $leave['user_list'],
            'status' => 0, //默认 0 待审批
            'annex' => $leave['image_path'] ?? '',
            'review_user_id' => $leave['review_user_id'],
            'remain_user' => $leave['remain_user'],
            'copy_user' => $leave['copy_user'],
        ];

         $ret = Leave::create($data);

         return $this->backLeaveData(true, [], ['leave_id' => $ret->leave_id]);
    }

    /**
     * 申请单验证和数据返回
     * @param $success
     * @param array $message
     * @param array $data
     * @return array
     */
    public function backLeaveData($success, $message = [], $data = [])
    {
       return ['success' => $success, 'message' => $message , 'data' => $data];
    }

    /**
     * 申请配置计算类型驱动
     * @param string $driver
     * @return mixed
     */
    public function driver(string $driver)
    {

        $lang = ucfirst(strtolower('cypher'));
        $driver = ucfirst(strtolower($driver));
        $nameSpace = str_replace('\Operate', '', __NAMESPACE__);

        $className = $nameSpace . "\\" . $lang . "\\" . $driver;
        return new $className();
    }

    public function addLeaveId($leaveId, $idArr = NULL)
    {
        $arr = json_decode($idArr);
        $arr[] = $leaveId;
        $arr = array_unique($arr);
        return json_encode($arr);
    }

    /**
     * 审核通过后, 上班打卡字段与下班打卡字段的设置
     * @param $leave
     * @param $startDay
     * @param $endDay
     */
    /*public static function getPunch($leave, $startDay, $endDay)
    {
        $ps = (int)str_replace(':', '', Leave::$startId[$leave->start_id]);
        $pe = (int)str_replace(':', '', Leave::$endId[$leave->end_id]);
        $arr1 = [
            //大于13:45,意味下午请假,则上班打卡字段为空,为后面打卡记录导入的上班打卡留位置
            'punch_start_time' => $ps >= 1345 ? NULL : Leave::$startId[$leave->start_id],
            //不等于20点,意味晚上还要回来上班,下班打卡字段为空,为后面打卡记录导入的下班打卡留位置
            'punch_end_time' => $pe != 2000 ? NULL : Leave::$endId[$leave->end_id]
        ];

        $arr2 = [
            'punch_start_time_num' => empty($arr1['punch_start_time']) ?
                NULL : strtotime(date('Y-m-d', $startDay) . ' ' . $arr1['punch_start_time']),
            'punch_end_time_num' => empty($arr1['punch_end_time']) ?
                NULL : strtotime(date('Y-m-d', $endDay) . ' ' . $arr1['punch_end_time']),
        ];
        return array_merge($arr1, $arr2);
    }*/

    public function setDailyDetail($leave)
    {
        $startDay = strtotime($leave->start_time);
        $endDay = strtotime($leave->end_time);

        $ifNeedUpdate = DailyDetail::whereBetween(\DB::raw('DATE_FORMAT(day, "%Y-%m-%d")'),
            [date('Y-m-d', $startDay), date('Y-m-d', $endDay)])->where('user_id', $leave->user_id)->get();

        $day = DataHelper::prDates($startDay, $endDay);
        $day = array_unique(array_merge($day, [$startDay, $endDay]));

        if (count($ifNeedUpdate) == 0) {
            foreach ($day as $d) {
                $data = [
                    'user_id' => $leave->user_id,
                    'day' => date('Y-m-d', $d),
                    'leave_id' => self::addLeaveId($leave->leave_id),
                    'punch_start_time' => NULL,
                    'punch_start_time_num' => NULL,
                    'punch_end_time' => NULL,
                    'punch_end_time_num' => NULL,
                ];
                DailyDetail::create($data);
            }
        }else {
            foreach ($ifNeedUpdate as $item) {
                $item->leave_id = self::addLeaveId($leave->leave_id, $item->leave_id);
                $item->save();
            }
        }
        /*$startDay = strtotime($leave->start_time);
        $endDay = strtotime($leave->end_time);

        $day = DataHelper::prDates($startDay, $endDay);

        if(!empty($day)) {
            foreach ($day as $k => $d) {
                $daily = DailyDetail::whereIn('day', [date('Y-m-d', $d)])->where(['user_id' => $leave->user_id])->first();
                if(!empty($daily->day)) continue;
                $data = [
                    'user_id' => $leave->user_id,
                    'day' => date('Y-m-d', $d),
                    'leave_id' => self::addLeaveId($leave->leave_id),
                    'punch_start_time' => Leave::$startId[1],
                    'punch_start_time_num' => strtotime(date('Y-m-d', $d) . ' ' . Leave::$startId[1]),
                    'punch_end_time' => Leave::$endId[3],
                    'punch_end_time_num' => strtotime(date('Y-m-d', $d) . ' ' . Leave::$endId[3]),
                ];

                DailyDetail::create($data);
            }
        }

        $startDaily = DailyDetail::where(['day' => date('Y-m-d', $startDay), 'user_id' => $leave->user_id])->first();
        $endDaily = DailyDetail::where(['day' => date('Y-m-d', $endDay), 'user_id' => $leave->user_id])->first();

        $punch = self::getPunch($leave, $startDay, $endDay);

        if($startDay == $endDay) {
            //插入或更新所需数据, 更新新的请假打卡也就是为空的字段,将新的请假id存入数组转为json存入数据表
            $startData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $startDay),
                'leave_id' => self::addLeaveId($leave->leave_id, $startDaily->leave_id ?? NULL),
                'punch_start_time' => $startDaily->punch_start_time ?? $punch['punch_start_time'],
                'punch_start_time_num' => $startDaily->punch_start_time_num ?? $punch['punch_start_time_num'],
                'punch_end_time' => $startDaily->punch_end_time ?? $punch['punch_end_time'],
                'punch_end_time_num' => $startDaily->punch_end_time_num ?? $punch['punch_end_time_num'],
            ];
            empty($startDaily->day) ? DailyDetail::create($startData) : $startDaily->update($startData);
        }

        if($startDay < $endDay) {
            $startData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $startDay),
                'leave_id' => self::addLeaveId($leave->leave_id, $startDaily->leave_id ?? NULL),
                'punch_start_time' => $startDaily->punch_start_time ?? $punch['punch_start_time'],
                'punch_start_time_num' => $startDaily->punch_start_time_num ?? $punch['punch_start_time_num'],
                'punch_end_time' => $startDaily->punch_end_time ?? Leave::$endId[3],
                'punch_end_time_num' => $startDaily->punch_end_time_num ?? strtotime(date('Y-m-d', $startDay) . ' ' . Leave::$endId[3]),
            ];
            empty($startDaily->day) ? DailyDetail::create($startData) : $startDaily->update($startData);

            $endData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $endDay),
                'leave_id' => self::addLeaveId($leave->leave_id, $endDaily->leave_id ?? NULL),
                'punch_start_time' => $endDaily->punch_start_time ?? Leave::$startId[1],
                'punch_start_time_num' => $endDaily->punch_start_time_num ??
                    strtotime(date('Y-m-d', $endDay) . ' ' . Leave::$startId[1]),
                'punch_end_time' => $endDaily->punch_end_time ?? $punch['punch_end_time'],
                'punch_end_time_num' => $endDaily->punch_end_time_num ?? $punch['punch_end_time_num'],
            ];
            empty($endDaily->day) ? DailyDetail::create($endData) : $endDaily->update($endData);
        }*/
        return true;
    }

    /**
     * 申请单 通过 操作
     * @param object $leave 申请单信息
     */
    public function leaveReviewPass($leave)
    {
        if(empty($leave->remain_user)) {
            $leave->update(['status' => 3, 'review_user_id' => 0]);
            self::setDailyDetail($leave);

        } else {
            $remainUser = json_decode($leave->remain_user, true);

            $reviewUserId = reset($remainUser);
            array_shift($remainUser);

            if(empty($remainUser)) {
                $remainUser = '';
            } else {
                $remainUser = json_encode($remainUser);
            }

            $leave->update(['status' => 1, 'review_user_id' => $reviewUserId, 'remain_user' => $remainUser]);
        }
    }

}