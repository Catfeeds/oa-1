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

    //审核状态
    const WAIT_REVIEW = 0;
    const ON_REVIEW = 1;
    const REFUSE_REVIEW = 2;
    const PASS_REVIEW = 3;
    const CANCEL_REVIEW = 4;
    const WAIT_EFFECTIVE = 5;
    //加班/调休时间点
    const WORK_TIME_POINT_1 = 1;
    const WORK_TIME_POINT_2 = 2;
    const WORK_TIME_POINT_3 = 3;
    const WORK_TIME_POINT_4 = 4;
    const WORK_TIME_POINT_5 = 5;


    public static $workTimePoint = [
        self::WORK_TIME_POINT_1 => '9点~12点',
        self::WORK_TIME_POINT_2 => '14点~18点',
        self::WORK_TIME_POINT_3 => '14点~20点',
        self::WORK_TIME_POINT_4 => '9点~18点',
        self::WORK_TIME_POINT_5 => '9点~20点',
    ];

    public static $workTimePointChar = [
        self::WORK_TIME_POINT_1 => ['start_time' => '9:00', 'end_time' => '12:00'],
        self::WORK_TIME_POINT_2 => ['start_time' => '14:00', 'end_time' => '18:00'],
        self::WORK_TIME_POINT_3 => ['start_time' => '14:00', 'end_time' => '20:00'],
        self::WORK_TIME_POINT_4 => ['start_time' => '9:00', 'end_time' => '18:00'],
        self::WORK_TIME_POINT_5 => ['start_time' => '9:00', 'end_time' => '20:00'],
    ];

    public static $types = [
        self::MY_LEAVE => '我的申请单',
        self::DEPT_LEAVE => '部门加班调休单',
        self::COPY_LEAVE => '抄送我的申请单',
    ];

    public static $startId = [
        1 => '09:00',
        2 => '13:45',
        3 => '19:00',
    ];

    public static $endId = [
        1 => '12:00',
        2 => '18:00',
        3 => '20:00',
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
    ];
    //排除已拒绝的状态
    public static $statusList = [
        self::WAIT_REVIEW ,
        self::ON_REVIEW ,
        self::PASS_REVIEW ,
        self::CANCEL_REVIEW ,
        self::WAIT_EFFECTIVE ,
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
    ];

    public function holidayConfig() {
        return $this->hasOne(HolidayConfig::class, 'holiday_id', 'holiday_id');
    }

    public static function getHolidayIdList()
    {
        return self::get(['holiday_id', 'leave_id'])->pluck('holiday_id', 'leave_id')->toArray();
    }

    //提取统计条件
    public static function leaveBuilder($year, $month)
    {
        return self::where('status', self::PASS_REVIEW)
            ->whereYear('start_time', $year)
            ->whereMonth('start_time', $month);
    }

    //带薪假:关联假期配置表 找出状态为已通过 且是福利假的假期
    public static function getSalaryLeaves($year, $month)
    {
        return self::leaveBuilder($year, $month)
            ->whereHas('holidayConfig', function ($query) {
                $query->where('is_boon', 1);
            })
            ->groupBy('user_id')
            ->get([DB::raw('sum(number_day) as s'), 'user_id'])->pluck('s', 'user_id')->toArray();
    }

    public static function noFull($year, $month)
    {
        return self::leaveBuilder($year, $month)
            ->whereHas('holidayConfig', function ($query) {
                $query->where('is_full', 1);
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

    //通过加班调休类型返回请假的id
    public static function getLeavesIdByChangeTypes($changeTypes, $year, $month)
    {
        return self::leaveBuilder($year, $month)
            ->whereHas('holidayConfig', function ($query) use ($changeTypes) {
                $query->where('apply_type_id', HolidayConfig::CHANGE)->whereIn('change_type', $changeTypes);
            })
            ->groupBy('user_id')
            ->get([DB::raw('group_concat(leave_id) as ls'), 'user_id'])->pluck('ls', 'user_id')->toArray();
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

}