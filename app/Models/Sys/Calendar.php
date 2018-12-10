<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/18
 * Time: 16:52
 * 日历表数据库
 */

namespace App\Models\Sys;

use App\Models\Attendance\DailyDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Traits\LogsActivity;

class Calendar extends Model
{
    use LogsActivity;

    protected $table = 'sys_attendance_calendar';

    protected $primaryKey = 'id';

    public static $week = [
        1 => '周一',
        2 => '周二',
        3 => '周三',
        4 => '周四',
        5 => '周五',
        6 => '周六',
        7 => '周日',
    ];

    protected $fillable = [
        'punch_rules_id',
        'year',
        'month',
        'day',
        'week',
        'memo',
    ];

    public function punchRules()
    {
        return $this->hasOne(PunchRules::class, 'id', 'punch_rules_id');
    }

    public static function getShouldComeDays($year, $month)
    {
        return self::whereHas('punchRules', function ($query) {
            $query->where('punch_type_id', PunchRules::NORMALWORK);
        })
            ->where(['year' => $year, 'month' => $month])
            ->count();
    }

    /**
     * @param string $minTs Y-n-j 格式的日期
     * @param $maxTs
     * @return Collection
     */
    public static function getCalendarArrWithPunchRules($minTs, $maxTs)
    {
        $MinTs = strtotime($minTs) - 3600 * 12;
        $MaxTs = strtotime($maxTs) + 3600 * 12;
        return Calendar::with('punchRules')
            ->whereBetween(\DB::raw('UNIX_TIMESTAMP(CONCAT(`year`,\'-\',`month`,\'-\',`day`))'), [$MinTs, $MaxTs])
            ->get(['*', \DB::raw('STR_TO_DATE(CONCAT(`year`,\'-\',`month`,\'-\',`day`), \'%Y-%m-%d\') as date')])->keyBy('date');
    }
}