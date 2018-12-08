<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 14:44
 */
namespace App\Models\Attendance;

use App\Models\Sys\HolidayConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
{
    use LogsActivity;

    protected $table = 'users_leave';

    protected $primaryKey = 'leave_id';

    const HASNOTIME = '1999-01-01';

    const MY_LEAVE = 1;
    const DEPT_LEAVE = 2;
    const COPY_LEAVE = 3;

    const LOGIN_INFO = 1;
    const LOGIN_VERIFY_INFO = 2;

    //是否统计
    const IS_STAT_YES = 0;
    const IS_STAT_NO = 1;

    //审核状态
    const WAIT_REVIEW = 0;
    const ON_REVIEW = 1;
    const REFUSE_REVIEW = 2;
    const PASS_REVIEW = 3;
    const CANCEL_REVIEW = 4;
    const WAIT_EFFECTIVE = 5;
    const SWITCH_REVIEW_ON = 6;
    const SWITCH_REVIEW_OFF = 7;
    const RETRACT_REVIEW = 8;
    const RESTART_REVIEW = 9;
    const BATCH_RETRACT_REVIEW = 10;
    const BATCH_RESTART_REVIEW = 11;

    //加班/调休时间点
    const WORK_TIME_POINT_1 = 1;
    const WORK_TIME_POINT_2 = 2;
    const WORK_TIME_POINT_3 = 3;
    const WORK_TIME_POINT_4 = 4;
    const WORK_TIME_POINT_5 = 5;

    public static $workTimePoint = [
        self::WORK_TIME_POINT_1 => '9点~20点',
        self::WORK_TIME_POINT_2 => '9点~18点',
        self::WORK_TIME_POINT_3 => '9点~12点',
        self::WORK_TIME_POINT_4 => '14点~20点',
        self::WORK_TIME_POINT_5 => '14点~18点',
    ];

    public static $workTimePointChar = [
        self::WORK_TIME_POINT_1 => ['start_time' => '9:00', 'end_time' => '20:00'],
        self::WORK_TIME_POINT_2 => ['start_time' => '9:00', 'end_time' => '18:00'],
        self::WORK_TIME_POINT_3 => ['start_time' => '9:00', 'end_time' => '12:00'],
        self::WORK_TIME_POINT_4 => ['start_time' => '14:00', 'end_time' => '20:00'],
        self::WORK_TIME_POINT_5 => ['start_time' => '14:00', 'end_time' => '18:00'],
    ];

    public static $types = [
        self::MY_LEAVE => '我的申请单',
        self::DEPT_LEAVE => '部门加班调休单',
        self::COPY_LEAVE => '抄送我的申请单',
    ];

    /**
     * 审核状态
     * @var array
     */
    public static $status = [
        self::WAIT_REVIEW => '待审核',
        self::ON_REVIEW => '审核中',
        self::REFUSE_REVIEW => '已拒绝',
        self::PASS_REVIEW => '已通过',
        self::CANCEL_REVIEW => '已取消',
        self::WAIT_EFFECTIVE => '已通过,待生效',
        self::SWITCH_REVIEW_ON => '转换假期生效',
        self::SWITCH_REVIEW_OFF => '转换假期取消',
        self::RETRACT_REVIEW => '已撤回',
    ];

    //查询排除已拒绝的状态
    public static $statusList = [
        self::WAIT_REVIEW ,
        self::ON_REVIEW ,
        self::PASS_REVIEW ,
        self::WAIT_EFFECTIVE ,
    ];
    //排除申请单可再提交的状态
    public static $applyList = [
        self::REFUSE_REVIEW,
        self::CANCEL_REVIEW,
        self::RETRACT_REVIEW,
    ];

    //申请单可撤回的状态
    public static $retractList = [
        self::WAIT_REVIEW,
        self::ON_REVIEW,
    ];
    //申请单可取消的状态
    public static $cancelList = [
        self::PASS_REVIEW,
        self::WAIT_EFFECTIVE,
    ];
    //申请单可重启的状态
    public static $restartList = [
        self::REFUSE_REVIEW,
        self::CANCEL_REVIEW,
        self::RETRACT_REVIEW,
    ];

    //员工可操作的状态列表
    public static $leaveOptStatus = [
        self::RETRACT_REVIEW,
        self::RESTART_REVIEW,
        self::BATCH_RETRACT_REVIEW,
        self::BATCH_RESTART_REVIEW,
    ];

    //审核人员可操作的状态列表
    public static $optStatus = [
        self::REFUSE_REVIEW,
        self::PASS_REVIEW,
        self::CANCEL_REVIEW,
        self::RETRACT_REVIEW,
        self::RESTART_REVIEW,
        self::BATCH_RETRACT_REVIEW,
        self::BATCH_RESTART_REVIEW,
    ];

    public static $leaveColor = [
        self::WAIT_REVIEW => 'badge',
        self::ON_REVIEW => 'badge badge-primary',
        self::REFUSE_REVIEW => 'badge badge-danger',
        self::PASS_REVIEW => 'badge badge-success',
        self::CANCEL_REVIEW => 'badge badge-danger',
        self::WAIT_EFFECTIVE => 'badge badge-info',
        self::RETRACT_REVIEW => 'badge badge-warning',
    ];

    //操作状态驱动标识
    public static $driverType = [
        self::REFUSE_REVIEW => 'refuse',
        self::PASS_REVIEW => 'pass',
        self::CANCEL_REVIEW => 'cancel',
        self::RETRACT_REVIEW => 'retract',
        self::RESTART_REVIEW => 'restart',
        self::BATCH_RETRACT_REVIEW => 'batchretract',
        self::BATCH_RESTART_REVIEW => 'batchrestart',
    ];

    protected $fillable = [
        'user_id',
        'step_id',
        'holiday_id',
        'start_time',
        'start_id',
        'end_time',
        'end_id',
        'number_day',
        'reason',
        'status',
        'user_list',
        'annex',
        'review_user_id',
        'remain_user',
        'copy_user',
        'exceed_day',
        'exceed_holiday_id',
        'step_user',

        'parent_id',
        'is_stat',

    ];

    public function holidayConfig() {
        return $this->hasOne(HolidayConfig::class, 'holiday_id', 'holiday_id');
    }

    public static function getHolidayIdList($noIncludeOffSwitch = false)
    {
        if ($noIncludeOffSwitch === false) {
            return self::get(['holiday_id', 'leave_id'])->pluck('holiday_id', 'leave_id')->toArray();
        }
        return self::where('status', '<>', self::SWITCH_REVIEW_OFF)
            ->get(['holiday_id', 'leave_id'])->pluck('holiday_id', 'leave_id')->toArray();
    }

    //提取统计条件
    public static function leaveBuilder($year, $month)
    {
        return self::where('status', self::PASS_REVIEW)
            ->whereYear('start_time', $year)
            ->whereMonth('start_time', $month);
    }

    public static function noFull($year, $month)
    {
        return self::leaveBuilder($year, $month)
            ->whereHas('holidayConfig', function ($query) {
                $query->where([['is_full', '=', 1], ['cypher_type', '<>', HolidayConfig::CYPHER_RECHECK]]);
            })
            ->groupBy('user_id')
            ->get([DB::raw('count(user_id) as a'), 'user_id'])
            ->pluck('a', 'user_id')->toArray();
    }

    public static function getNoSalaryLeaves($year, $month)
    {
        return self::leaveBuilder($year, $month)
            ->whereHas('holidayConfig', function ($query1) {
                $query1->where([['is_boon', '<>', 1], ['apply_type_id', '=', HolidayConfig::LEAVEID]]);
            })
            ->groupBy('user_id')
            ->get([DB::raw('sum(number_day) as s'), 'user_id'])->pluck('s', 'user_id')->toArray();
    }


    public static function getAttrByLeaveIds($leaveIds, $attr)
    {
        $arr = [];
        $leaves = self::whereIn('leave_id', $leaveIds)->with('holidayConfig')->get();
        foreach ($leaves as $leaf) {
            $arr[] = $leaf->holidayConfig->$attr;
        }
        return $arr;
    }

    public static function leaveColorStatus($status)
    {
        return '<span class="'. (self::$leaveColor[$status] ?? '') .'">'.self::$status[$status] .'</span>';
    }


}