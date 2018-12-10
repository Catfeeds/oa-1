<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 11:18
 */
namespace App\Components\AttendanceService\Cypher;

use App\Components\Helper\DataHelper;
use App\Http\Components\Helpers\AttendanceHelper;
use App\Http\Components\Helpers\OperateLogHelper;
use App\Models\Attendance\Leave;
use App\Models\Sys\Dept;
use App\Models\Sys\ReviewStepFlow;
use App\User;

class Cypher
{
    public function check($holidayConfig, $numberDay)
    {
        //带薪假，假期下限天数判断
        $minDay = $holidayConfig->under_day;
        if(!empty($minDay) && $numberDay < $minDay) {
            return $this->backCypherData(false, ['end_time' => '申请假期最短为'. $minDay. '天']);
        }

        return $this->backCypherData(true);
    }

    public function getUserHoliday($entryTime, $userId, $holidayConfig)
    {
        return ['status' => 1, 'show_memo' => true, 'memo' => $holidayConfig->memo];
    }
    /**
     * 申请单验证和数据返回
     * @param $success
     * @param array $message
     * @param array $data
     * @return array
     */
    public function backCypherData($success, $message = [], $data = [])
    {
        return ['success' => $success, 'message' => $message , 'data' => $data];
    }

    public function getDaysByScope($scope, $userId, $holidays)
    {
        return $this->getPaidDaysByScope($scope, $userId, $holidays);
    }

    /**
     * 获取scope时间段内请带薪假/无薪假的天数
     * @param $scope
     * @param $userId
     * @param $holidays
     * @return int
     */
    public function getPaidDaysByScope($scope, $userId, $holidays)
    {
        $days = 0;
        foreach ($holidays as $holiday) {
            $days = $days + $this->getUserMonthHoliday($scope, $userId, $holiday)['apply_days'];
        }
        return $days;
    }

    /**
     * 获取scope时间内加班/调休的天数统计
     * @param $scope
     * @param $userId
     * @param $holidays
     * @param $applyType
     * @return int|mixed
     */
    /*public function getOverDaysByScope($scope, $userId, $holidays, $applyType)
    {
        $holidayIds = [];
        foreach ($holidays as $holiday) {
            if ($holiday->apply_type_id == $applyType) {
                $holidayIds[] = $holiday->holiday_id;
            }
        }
        $leaveIds = Leave::leaveBuilder(date('Y', strtotime($scope['start_time'])), date('m', strtotime($scope['start_time'])))
            ->whereIn('holiday_id', $holidayIds)->where('user_id', $userId)->get()->pluck('leave_id')->toArray();

        return AttendanceHelper::selectChangeInfo('', '', $leaveIds);
    }*/


    /**
     * 按员工入职时间维度获取带薪假期
     * @param $entryTime
     * @param $userId
     * @param $holiday
     * @return mixed 返回天数
     */
    public function getUserPayableDayToEntryTime($entryTime, $userId, $holiday)
    {
        //获得带薪假期配置信息
        $claimTime = DataHelper::dateTimeAddToFormula($entryTime, $holiday->payable_claim_formula);
        //入职未满配置时间范围，返回天数为0
        if(empty($entryTime) || strtotime($claimTime) > time()) return 0;
        //获取带薪假期重置配置信息
        $resetTime = DataHelper::dateTimeAddToFormula($entryTime, $holiday->payable_reset_formula);

        //开始默认为带薪起效时间
        $startTime= $claimTime;
        //结束默认为带薪重置时间
        $endTime = $resetTime;

        //到期时间之后，重置开始时间和到期时间
        if(strtotime($endTime) < time()) {
            $startTime = DataHelper::dateTimeFormat('now', 'Y') . '-' . DataHelper::dateTimeFormat($endTime, 'm-d H:i:s');
            $endTime = DataHelper::dateTimeAddToFormula($startTime, $holiday->payable_reset_formula);
        }

        return $this->selectLeaveInfo($startTime, $endTime, $userId, $holiday);
    }

    /**
     * 按自然周期时间维度获取带薪假期
     * @param $userId
     * @param $holiday
     * @return mixed
     */
    public function getUserPayableDayToNaturalCycleTime($entryTime, $userId, $holiday)
    {
        $claimTime = DataHelper::dateTimeAddToFormula($entryTime, $holiday->payable_claim_formula);
        //入职未满配置时间范围，返回天数为0
        if(empty($entryTime) || strtotime($claimTime) > time()) return 0;

        $resetDate = self::resolveCycleConfigFormula($holiday->payable_reset_formula);
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

    public function resolveCycleConfigFormula($formula)
    {
        $format = json_decode($formula, true);
        if(empty($format)) return [];

        $date = [];
        foreach ($format as $k => $v) {
            if(empty($v)) continue;
            if($v <10) $v = '0'.$v;
            switch ($k) {
                case 0 :
                    $date['m'] = $v;
                    break;
                case 1 :
                    $date['d'] = $v ;
                    break;
                case 2 :
                    $date['h'] = $v ;
                    break;
                case 3 :
                    $date['i'] = $v ;
                    break;
                case 4 :
                    $date['s'] = $v;
                    break;
            }
        }

        return $date;
    }

    public function getUserPayableDayToNoCycleTime($userId, $holiday)
    {
        $startTime = date('Y').'-01-01';
        $endTime = date('Y').'-12-31';
        return $this->selectLeaveInfo($startTime, $endTime, $userId, $holiday);
    }


    /**
     * 获取员工 月类型 记录信息
     * @param $userId
     * @param $holiday
     * @return mixed
     */
    public function getUserMonthHoliday($request, $userId, $holiday)
    {
        $startDay =  date('Y-m-01', strtotime($request['start_time']));
        $endDay =  date('Y-m-t', strtotime($request['end_time']));

        return $this->selectLeaveInfo($startDay, $endDay, $userId, $holiday);
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
        $userLeaveLog = Leave::select([
            \DB::raw('SUM(number_day) number_day'),
            \DB::raw('SUM(exceed_day) exceed_day'),
            \DB::raw('count(*) count_num'),
        ])
            ->where('start_time', '>=', $startDay)
            ->where('end_time', '<=', $endDay)
            ->whereIn('status', [Leave::PASS_REVIEW, Leave::WAIT_REVIEW, Leave::ON_REVIEW, Leave::WAIT_EFFECTIVE])
            ->where([
                'user_id' => $userId,
                'holiday_id' => $holiday->holiday_id,
            ])->groupBy('user_id')->first();

        $numberDay = empty($userLeaveLog->number_day) ? $holiday->up_day : $holiday->up_day - ($userLeaveLog->number_day - $userLeaveLog->exceed_day);

        return ['number_day' => $numberDay, 'count_num' => $userLeaveLog->count_num ?? 0, 'apply_days' => $userLeaveLog->number_day ?? 0];
    }

    /**
     * 显示审核步骤
     * @param $holidayId
     * @param int $numberDay
     * @return string
     */
    public function showLeaveStep($holidayId, $numberDay = 0.5)
    {
        $leaderStepUid = $step =  [];

        $steps = ReviewStepFlow::with('config')
            ->where(['child_id' => $holidayId])
            ->orderBy('min_num', 'asc')
            ->get()
            ->toArray();

        if(empty($numberDay) && !empty($steps)) $steps[] = $steps[0];

        foreach ($steps as $sk => $sv) {
            if($sv['min_num'] <= $numberDay && $sv['max_num'] >= $numberDay) {
                $step = $sv;
                break;
            }
        }

        if(!empty($step)) {
            foreach ($step['config'] as $lk => $lv) {
                //指定人类型
                if((int)$lv['assign_type'] === 0) {
                    $leaderStepUid[$lv['step_order_id']] = (User::getUsernameAliasList()[$lv['assign_uid']] ?? '')
                        .' <input type="hidden" name="step_user['.$lv['step_order_id'].']" value="'.$lv['assign_uid'].'">';
                }
                //查询部门,如果有存在2级部门时，主部门也成员要查询
                $checkDept = Dept::where(['dept_id' => \Auth::user()->dept_id])->first();
                if(empty($checkDept->dept_id)) continue;
                $deptIds = [\Auth::user()->dept_id];
                if(!empty($checkDept->parent_id)) $deptIds = [\Auth::user()->dept_id, $checkDept->parent_id];
                $dept =  sprintf(' and dept_id in (%s)', implode(',', $deptIds));
                if((int)$lv['group_type_id'] === 1) $dept = '';
                //指定角色类型
                $roleId = sprintf('JSON_EXTRACT(role_id, "$.id_%d") = "%d"', $lv['assign_role_id'], $lv['assign_role_id']);
                $userLeader = User::where(['status' => User::STATUS_ENABLE])->whereRaw($roleId . $dept)->get()->toArray();
                if((int)$lv['assign_type'] === 1 && empty($userLeader)) $leaderStepUid[$lv['step_order_id']]  = '';
                if((int)$lv['assign_type'] === 1 && !empty($userLeader)) {
                    //是否可以修改审批人 角色分配大于等于2人
                    if((int)$step['is_modify'] === ReviewStepFlow::MODIFY_YES && count($userLeader) >= 2) {
                        $opt = [];
                        foreach ($userLeader as $uk => $uv) {
                            $opt[] = '<option value="'.$uv['user_id'].'">'.$uv['alias'] . '('. $uv['username'] . ')</option>';
                        }

                        $leaderStepUid[$lv['step_order_id']]  = '<select id="copy_user" style="width: 10em" name="step_user['.$lv['step_order_id'].']">' .
                            implode(' ', $opt)
                            .'</select> <input type="hidden" name="is_edit_step" value="'.ReviewStepFlow::MODIFY_YES.'">
                            <input type="hidden" name="step_id" value="'.$step['step_id'].'">';
                    } else {
                        $leaderStepUid[$lv['step_order_id']] = (User::getUsernameAliasList()[$userLeader[0]['user_id']] ?? '')
                            .' <input type="hidden" name="step_user['.$lv['step_order_id'].']" value="'.$lv['assign_uid'].'">';;
                    }
                }
            }
        }

        $leaderStepUid = array_filter($leaderStepUid);
        ksort($leaderStepUid);
        return empty($leaderStepUid) ? '未设置' : implode('>>', $leaderStepUid);
    }

    /**
     * 获取申请天数
     * @param $params
     * @return int|number
     */
    public function getLeaveNumberDay($params)
    {
        $numberDay = 0;

        if(empty($params['startTime']) || empty($params['startId']) || empty($params['endTime']) || empty($params['endId'])) return $numberDay;

        $numberDay = DataHelper::leaveDayDiff($params['startTime'], $params['startId'], $params['endTime'], $params['endId']);

        return $numberDay;
    }

    /**
     *  显示申请时间
     * @param $params
     * @return array
     */
    public function spliceLeaveTime($params)
    {
        return [
            'time'=> DataHelper::dateTimeFormat($params['time'] .' '. $params['timeId'], 'Y-m-d H:i'),
            'number_day' => $params['number_day'] . '天',
        ];
    }

    /**
     * 重新组装时间
     * @return array
     */
    public function buildUpLeaveTime($startTime, $endTime, $startId, $endId)
    {
        return [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'start_id' => $startId,
            'end_id' => $endId,
            'start_timeS' => trim($startTime .' '. $startId),
            'end_timeS' => trim($startTime .' '. $endId),
        ];
    }

    /**
     * 微信消息内容
     * @param $msgArr
     */
    public function sendWXContent($msgArr)
    {
        $content =  '【'.$msgArr['applyType'].'】'.$msgArr['notice'].'
        申请事项：'.$msgArr['holiday'].'
        申请人：'.$msgArr['username'].'
        所属部门：'.$msgArr['dept'].'
        开始时间：'.$msgArr['start_time'].' '.$msgArr['start_id'].'
        结束时间：'.$msgArr['end_time'].' '.$msgArr['end_id'].'
        折合时间：'.$msgArr['number_day'].'
        点击此处查看申请详情[<a href = "'.$msgArr['url'].'">点我前往</a>]';

        //企业微信通知审核人员
        OperateLogHelper::sendWXMsg($msgArr['send_user'], $content);
    }

}