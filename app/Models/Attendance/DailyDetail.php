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
use App\Models\Sys\PunchRules;
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

    //提取正常天数的条件 上下班打卡没有请假,或lead_id为补打卡类型
    public static function normalBuilder($year, $month)
    {
        return self::where([['punch_start_time', '<>', NULL], ['punch_end_time', '<>', NULL]])
            ->where(function ($q1) {
                $q1->where('leave_id', NULL)
                    ->orWhere(function ($q2) {
                        $q2->whereHas('leave', function ($q3) {
                            $q3->whereHas('holidayConfig', function ($q4) {
                                $q4->where('apply_type_id', HolidayConfig::RECHECK);
                            });
                        });
                    });
            })
            ->whereYear('day', $year)->whereMonth('day', $month)->groupBy('user_id');
    }

    //统计正常天数
    public static function getNormalCome($year, $month)
    {
        return self::normalBuilder($year, $month)->get([DB::raw('count(*) as come'), 'user_id'])
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