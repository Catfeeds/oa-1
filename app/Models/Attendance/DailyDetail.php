<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/21
 * Time: 9:53
 * 每日考勤明细表
 */

namespace App\Models\Attendance;

use App\Http\Components\Helpers\AttendanceHelper;
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

    public static function getBeLateNum($year, $month)
    {
        return self::builder($year, $month)
            ->get([DB::raw('sum(heap_late_num) as late'), 'user_id'])
            ->pluck('late', 'user_id')
            ->toArray();
    }

    public static function getDeductNum($year, $month)
    {
        return self::builder($year, $month)
            ->get([DB::raw('sum(deduction_num) as deduct'), 'user_id'])
            ->pluck('deduct', 'user_id')
            ->toArray();
    }

    /**
     * 获取计算实到天数所需的条件
     * @param $year
     * @param $month
     * @return array 返回[正常天数打卡, 从请假与正常打卡混合的天数中获取正常打卡总数]
     */
    public static function calculateCome($year, $month)
    {
        //没请假, 有打卡
        $punch = self::builder($year, $month)->where('leave_id', NULL)
            ->get([DB::raw('(count(punch_start_time) + count(punch_end_time)) * 0.5 as s'), 'user_id'])
            ->pluck('s', 'user_id')->toArray();

        $factLeaves = Leave::leaveBuilder($year, $month)
            ->groupBy('user_id')->get([DB::raw('sum(number_day) as d'), 'user_id'])
            ->pluck('d', 'user_id')->toArray();

        $leavePunch = self::builder($year, $month)->where('leave_id', '<>', NULL)
            ->get([DB::raw('(count(punch_start_time) + count(punch_end_time)) * 0.5 as s'), 'user_id'])
            ->pluck('s', 'user_id')->toArray();

        //因为请到18:00, 20:00在请假表都算1天,按打卡表计算与请假表计算存在误差, 计算差值,弥补误差
        $remainClock = [];
        $clock18s = Leave::leaveBuilder($year, $month)->where(['end_id' => 2])->get();
        foreach ($clock18s as $clock18) {
            $ps = (int)str_replace(':', '', Leave::$startId[$clock18->start_id]);
            $ed = date('j', strtotime($clock18->end_time));
            $sd = date('j', strtotime($clock18->start_time));

            //重新获得请到下午18:00的假期天数
            $number_day = ($ed - $sd - 1) + 0.5 + ($ps < 1200 ? 1 : 0.5);
            //获得18:00请假表与计算的18:00的差值
            if ($number_day < $clock18->number_day) {
                $remainClock[$clock18->user_id] = ($clock18->number_day - $number_day) + ($remainClock[$clock18->user_id] ?? 0);
            }
        }

        $remain = [];
        foreach (User::all() as $user) {
            $remain[$user->user_id] = ($leavePunch[$user->user_id] ?? 0) - ($factLeaves[$user->user_id] ?? 0) + ($remainClock[$user->user_id] ?? 0);
        }

        return [$punch, $remain];
    }

}