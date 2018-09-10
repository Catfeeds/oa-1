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

    const WAIT_REVIEW = 0;
    const ON_REVIEW = 1;
    const REFUSE_REVIEW = 2;
    const PASS_REVIEW = 3;
    const CANCEL_REVIEW = 4;

    public static $types = [
        self::MY_LEAVE => '我的申请单',
        self::DEPT_LEAVE => '部门加班调休单',
        self::COPY_LEAVE => '抄送我的申请单',
    ];

    public static $startId = [
        1 => '09:00',
        2 => '13:45',
        3 => '19:00',
        0 => ''
    ];

    public static $endId = [
        1 => '12:00',
        2 => '18:00',
        3 => '20:00',
        0 => ''
    ];

    /**
     * 审核状态
     * @var array
     */
    public static $status = [
        0 => '待审核',
        1 => '审核中',
        2 => '已拒绝',
        3 => '已通过',
        4 => '已取消',
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
    ];

    public function holidayConfig() {
        return $this->hasOne(HolidayConfig::class, 'holiday_id', 'holiday_id');
    }

    public static function getHolidayIdList()
    {
        return self::get(['holiday_id', 'leave_id'])->pluck('holiday_id', 'leave_id')->toArray();
    }

    //带薪假:关联假期配置表 找出状态为已通过 且是福利假的假期
    public static function getSalaryLeaves($year, $month){
        return self::whereHas('holidayConfig', function ($query){
            $query->where('is_boon', 1);
        })
            ->where('status', 3)->whereYear('start_time', $year)->whereMonth('start_time', $month)
            ->get();
    }

    //加班调休与无薪假(请假):不是福利假,已通过,不是补打卡,当月
    public static function getNoSalaryLeaves($year, $month){
        return self::whereHas('holidayConfig', function ($query){
            $query->where([['is_boon', '<>', 1], ['apply_type_id', '<>', HolidayConfig::RECHECK]]);
        })
            ->with('holidayConfig')
            ->where('status', '=', 3)
            ->whereYear('start_time', $year)->whereMonth('start_time', $month)->get();
    }

    public static function getReCheckSum($year, $month){
        return self::whereHas('holidayConfig', function ($query){
            $query->where('apply_type_id', HolidayConfig::RECHECK);
        })
            ->where('status', 3)
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)
            ->whereRaw('(start_time IS NOT NULL OR end_time IS NOT NULL)')
            ->groupBy('user_id')
            ->get([DB::raw('(count(start_time) + count(end_time)) as a'), 'user_id'])
            ->pluck('a', 'user_id')->toArray();
    }

}