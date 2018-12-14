<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/3
 * Time: 19:52
 * 补打卡
 */

namespace App\Components\AttendanceService\Operate;

use App\Components\AttendanceService\AttendanceInterface;
use App\Components\Helper\DataHelper;
use App\Http\Components\Helpers\PunchHelper;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;
use App\User;
use Illuminate\Foundation\Validation\ValidatesRequests;


class Recheck extends Operate implements AttendanceInterface
{
    use  ValidatesRequests;

    public function checkLeave($request): array
    {
        //补打卡不需要end_time
        unset($this->_validateRule['end_time']);
        $this->validate($request, $this->_validateRule);
        $p = $request->all();

        //假期配置ID
        $holidayId = $p['holiday_id'] ?? '';
        //批量调休人员名单
        $copyUser = $p['copy_user'] ?? NULL;

        //补下班打卡
        if (empty($startTime)) {
            $this->validate($request, array_merge($this->_validateRule, [
                'start_time' => 'required',
            ]), ['请选择有效的时间范围']);
        }

        $startTime = $endTime =  $startId = $endId = NULL;

        $config = HolidayConfig::where(['holiday_id' => $holidayId, 'apply_type_id' => HolidayConfig::RECHECK])->first();

        if(empty($config->holiday_id)) {
            return $this->backLeaveData(false, ['holiday_id' => trans('无效申请ID')]);
        }

        if($config->punch_type === HolidayConfig::GO_WORK) {
            $startTime = $p['start_time'];
            $startId = DataHelper::dateTimeFormat($p['start_time'], 'H:i');

        }

        if($config->punch_type === HolidayConfig::OFF_WORK) {
            $endTime = $p['start_time'];
            $endId = DataHelper::dateTimeFormat($p['start_time'], 'H:i');
        }

        //批量抄送组织可查询的JSON数据
        if (!empty($copyUser)) {
            $copyIds = [];
            foreach ($copyUser as $d => $v) {
                $roleIds['id_' . $v] = $v;
            }
            $copyUser = json_encode($copyIds);
        }
        $data = [
            'start_time'        => $startTime,
            'end_time'          => $endTime,
            'holiday_id'        => (int)$holidayId,
            'number_day'        => 0,//补打卡默认天数未0
            'copy_user'         => $copyUser,
            'start_id'          => $startId,
            'end_id'            => $endId,
            'exceed_day'        => NULL,
            'exceed_holiday_id' => NULL,
        ];

        return $this->backLeaveData(true, [], $data);
    }

    public function getLeaveStep($request, $numberDay): array
    {
        return parent::getLeaveStep($request, $numberDay);
    }

    /**
     * 创建申请单
     * @param array $leave
     * @return array
     */
    public function createLeave(array $leave): array
    {
        return parent::createLeave($leave);
    }

    /**
     * @param array $leave
     * @return array
     */
    public function updateLeave(array $leave): array
    {
        return parent::updateLeave($leave);
    }

    /**
     * @param $leave
     */
    public function setDailyDetail($leave)
    {
        $startDay = strtotime($leave->start_time);
        $endDay = strtotime($leave->end_time);

        //上下班都要补打卡的情况
        /*if (date('Y-m-d', $startDay) == date('Y-m-d', $endDay)) {
            $daily = DailyDetail::whereIn('day', [date('Y-m-d', $startDay)])
                ->where(['user_id' => $leave->user_id])
                ->first();

            $daily->user_id = $leave->user_id;
            $daily->day = date('Y-m-d', $startDay);
            $daily->leave_id = self::addLeaveId($leave->leave_id, $daily->leave_id);
            $daily->punch_start_time = date('H:i', $startDay);
            $daily->punch_start_time_num = $startDay;
            $daily->punch_end_time = date('H:i', $endDay);
            $daily->punch_end_time_num = $endDay;

            //$daily->save();
            $this->updateSwitchInLeave($daily);
            return;
        }*/
        //上班补打卡
        if ($leave->holidayConfig->punch_type === 1) {
            $daily = DailyDetail::whereIn('day', [date('Y-m-d', $startDay)])
                ->where(['user_id' => $leave->user_id])
                ->first();

            $daily->user_id = $leave->user_id;
            $daily->day = date('Y-m-d', $startDay);
            $daily->leave_id = self::addLeaveId($leave->leave_id, $daily->leave_id);
            if (date('H:i', $startDay) < $daily->punch_start_time) {
                $daily->punch_end_time = $daily->punch_start_time;
                $daily->punch_end_time_num = $daily->punch_start_time_num;
            }
            $daily->punch_start_time = date('H:i', $startDay);
            $daily->punch_start_time_num = $startDay;

            $daily->save();
            $this->updateSwitchInLeave($daily, $leave->holidayConfig->punch_type);
        }

        //下班补打卡
        if ($leave->holidayConfig->punch_type === 2) {
            $daily = DailyDetail::whereIn('day', [date('Y-m-d', $endDay)])
                ->where(['user_id' => $leave->user_id])
                ->first();

            $daily->user_id = $leave->user_id;
            $daily->day = date('Y-m-d', $endDay);
            $daily->leave_id = self::addLeaveId($leave->leave_id, $daily->leave_id);
            if (date('H:i', $endDay) > $daily->punch_start_time) {
                $daily->punch_end_time = date('H:i', $endDay);
                $daily->punch_end_time_num = $endDay;
            }

            $daily->save();
            $this->updateSwitchInLeave($daily, $leave->holidayConfig->punch_type);
        }
    }

    public function updateSwitchInLeave($dailyDetail, $punch_type = '')
    {
        /*switch ($punch_type) {
            case 1: $is = Leave::LATE_ABSENTEEISM;break;
            case 2: $is = Leave::EARLY_ABSENTEEISM;break;
            default: $is = Leave::ALLDAY_ABSENTEEISM;break;
        }*/
        /*$switch = Leave::where('user_id', $dailyDetail->user_id)
            ->where(\DB::raw('STR_TO_DATE(start_time, \'%Y-%m-%d\')'), $dailyDetail->day)
            ->where([['status', '=', Leave::SWITCH_REVIEW_ON], ['number_day', '>', 0]])->get();
        if ($switch->count() == 0) return;*/

        $startDate = $dailyDetail->day;
        $lastDate = DataHelper::dateTimeAdd($startDate, '1D', 'Y-m-d', 'sub');
        $endDate = date('Y-m-t', strtotime($startDate));
        $bufferArr = $newData = $newDailyDetail = $columnValues = $cv = [];
        $columns = NULL;
        $punchHelper = PunchHelper::getInstance($startDate, $endDate);
        $data = DailyDetail::whereBetween('day', [$startDate, $endDate])->where('user_id', $dailyDetail->user_id)->orderBy('day', 'asc')->get()->keyBy('day');
        $alias = User::find($dailyDetail->user_id)->alias;

        //因为补打卡,所以之后的时间会发生改变
        foreach ($data as $datum) {
            list($lastDayEndTime, $startTime, $endTime) = $punchHelper->dealLastDayEnd($datum->day,
                [3 => $alias, 5 => $datum->punch_start_time, 6 => $datum->punch_end_time]);
            $newData[$datum->day] = ['start_time' => $startTime, 'end_time' => $endTime, 'ts' => $datum->day, 'alias' => $alias];
            if (isset($lastDayEndTime)) {
                $data[date('Y-m-d', strtotime("-1 day $datum->day"))]['end_time'] = $lastDayEndTime;
                unset($lastDayEndTime);
            }
        }

        $lastRemainBuffer = DailyDetail::whereBetween('day', [date('Y-m-01', strtotime($lastDate)), $lastDate])
                ->where('user_id', $dailyDetail->user_id)->orderBy('lave_buffer_num', 'asc')
                ->first(['lave_buffer_num'])->lave_buffer_num ?? 0;
        //获取旧的转换假期id
        $oldSwitchLeaves = Leave::whereBetween(\DB::raw('DATE_FORMAT(start_time, \'%Y-%m-%d\')'), [$startDate, $endDate])->where('user_id', $dailyDetail->user_id)
            ->where('is_switch', '<>', Leave::NO_SWITCH)->get()->pluck('leave_id')->toArray();
        \DB::beginTransaction();
        //删除
        Leave::whereIn('leave_id', $oldSwitchLeaves)->delete();

        foreach ($newData as $d) {
            //排除旧的转换假期id
            $leaveIdsNoSwitch = array_diff(json_decode($data[$d['ts']]->leave_id ?? '[]'), $oldSwitchLeaves);
            $data[$d['ts']]->leave_id = empty($leaveIdsNoSwitch) ? NULL : json_encode($leaveIdsNoSwitch);
            $deducts = $punchHelper->fun_($bufferArr, $d, $data[$d['ts']], $lastRemainBuffer);
            $bufferArr = $deducts['bufferArr'];
            $switchLeaveIds = $punchHelper->storeDeductInLeave($deducts['deducts'], $dailyDetail->user_id, $d['ts']);

            $newDailyDetail[] = [
                'id'                   => $data[$d['ts']]->id,
                'user_id'              => $dailyDetail->user_id,
                'day'                  => $d['ts'],
                'punch_start_time'     => $d['start_time'],
                'punch_start_time_num' => DataHelper::convertTime($d['ts'], $d['start_time']),
                'punch_end_time'       => $d['end_time'],
                'punch_end_time_num'   => DataHelper::convertTime($d['ts'], $d['end_time']),
                'heap_late_num'        => $deducts['deducts']['deduct']['minute'] ?? 0,
                'lave_buffer_num'      => $deducts['deducts']['remain_buffer'] ?? 0,
                'deduction_num'        => $deducts['deducts']['deduct']['score'] ?? 0,
                'leave_id'             => empty($switchLeaveIds) ? NULL : json_encode($switchLeaveIds),
                'updated_at'           => date('Y-m-d H:i:s'),
            ];
            //DailyDetail::find($data[$d['ts']]->id)->update($newDailyDetail);
        }
        PunchHelper::batchUpdate('attendance_daily_detail', $newDailyDetail);
        \DB::commit();
    }

    /**
     * @param string $leaveId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getLeaveView($leaveId = '')
    {
        $title = trans('att.补打卡');
        $reviewUserId = '';

        //申请单重启
        if (!empty($leaveId) && \Entrust::can(['leave.restart'])) {
            $leave = Leave::with('holidayConfig')->findOrFail($leaveId);
            if ((int)$leave->user_id !== \Auth::user()->user_id || !in_array($leave->status, Leave::$restartList)) {
                flash('请勿非法操作', 'danger');
                return redirect()->route('leave.info');
            }
            $copyUserIds = json_decode($leave->copy_user, true);
            $title = trans('att.重启补打卡');
        }

        $allUsers = User::where(['status' => User::STATUS_ENABLE])->get();
        $time = date('Y-m-d H:i', time());

        $holidayList = HolidayConfig::where(['apply_type_id' => HolidayConfig::RECHECK])
            ->orderBy('sort', 'asc')
            ->get(['holiday_id', 'show_name'])
            ->pluck('show_name', 'holiday_id')
            ->toArray();
        $daily = DailyDetail::where(['user_id' => \Auth::user()->user_id, 'day' => request()->day])->first();

        return view('attendance.leave.recheck', compact('title', 'time', 'holidayList', 'leave', 'copyUserIds', 'reviewUserId', 'daily', 'allUsers'));

    }
}