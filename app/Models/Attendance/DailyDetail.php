<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/21
 * Time: 9:53
 * 每日考勤明细表
 */

namespace App\Models\Attendance;

use App\Models\Sys\HolidayConfig;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class DailyDetail extends Model
{
    use LogsActivity;

    protected $table = 'attendance_daily_detail';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'day',
        'leave_id',
        'punch_start_time',
        'punch_start_time_num',
        'punch_end_time',
        'punch_end_time_num',
        'heap_late_num',
        'lave_buffer_num',
        'deduction_num',
    ];

    public function leave()
    {
        return $this->hasOne(Leave::class, 'leave_id', 'leave_id');
    }

    //提取统计条件
    public static function builder($year, $month)
    {
        return self::whereYear('day', $year)->whereMonth('day', $month)->groupBy('user_id');
    }

    //提取实到天数的条件 上下班打卡没有请假,或上下班没打卡但补打卡
    public static function ActuallyBuilder($year, $month)
    {
        return self::where(function ($q) {
            $q->where([['punch_start_time', '<>', NULL], ['punch_end_time', '<>', NULL], ['leave_id', '=', NULL]])
                ->orWhere(function ($query) {
                    $query->whereRaw('(punch_start_time IS NULL OR punch_end_time IS NULL)')
                        ->whereHas('leave', function ($q1) {
                            $q1->whereHas('holidayConfig', function ($q2) {
                                $q2->where('apply_type_id', HolidayConfig::RECHECK);
                            });
                        });
                });
        })
            ->whereYear('day', $year)->whereMonth('day', $month)
            ->groupBy('user_id');
    }

    //统计实到天数
    public static function getActuallyCome($year, $month)
    {
        return self::ActuallyBuilder($year, $month)->get([DB::raw('count(*) as come'), 'user_id'])
            ->pluck('come', 'user_id')
            ->toArray();
    }

    public static function getBeLateNum($year, $month)
    {
        return self::builder($year, $month)->get([DB::raw('sum(heap_late_num) as late'), 'user_id'])
            ->pluck('late', 'user_id')
            ->toArray();
    }

    public static function getDeductNum($year, $month)
    {
        return self::builder($year, $month)->get([DB::raw('sum(deduction_num) as deduct'), 'user_id'])
            ->pluck('deduct', 'user_id')
            ->toArray();
    }

}