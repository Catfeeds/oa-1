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

        $detail = self::whereYear('day', $year)->whereMonth('day', $month)->where('leave_id', '<>', NULL)->get();

        //计算打卡表中punch_start_time与punch_end_time不为空的个数
        $leaveAndPunches = [];
        foreach ($detail as $item) {
            $leaveAndPunches[$item->user_id] = ($leaveAndPunches[$item->user_id] ?? 0) +
                isset($item->punch_start_time) + isset($item->punch_end_time);
        }

        //获取实际请假的天数
        $factLeaves = Leave::leaveBuilder($year, $month)
            ->groupBy('user_id')->get([DB::raw('sum(number_day) as d'), 'user_id'])
            ->pluck('d', 'user_id')->toArray();

        //计算请到18:00的假期id数
        $clock18 = Leave::leaveBuilder($year, $month)->where(['end_id' => 2])->groupBy('user_id')
            ->get([DB::raw('count(leave_id) as l'), 'user_id'])->pluck('l', 'user_id')->toArray();

        $remain = [];
        foreach ($leaveAndPunches as $user_id => $leaveAndPunch) {
            /**
             * 假如请下午半天, 这天的打卡记录为上午正常打卡, 下午请假打卡
             * 所以计算的办法为:计算打卡表的上下班打卡个数(为2) * 0.5 - 请假天数(为0.5) = 0.5
             * 但请到18:00时, 请假表显示的天数为1天, 请到20:00的请假天数也为1天 存在误差 补充误差天数
             */
            $remain[$user_id] = $leaveAndPunch * 0.5 + ($clock18[$user_id] ?? 0) * 0.5 - ($factLeaves[$user_id] ?? 0);
        }
        return [$punch, $remain];
    }

}