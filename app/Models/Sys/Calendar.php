<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/18
 * Time: 16:52
 * 日历表数据库
 */

namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Calendar extends Model
{
    use LogsActivity;

    protected $table = 'calendar';

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

    //统计打卡类型为当月正常上班的天数
    public static function getShouldComeDays($year, $month)
    {
        return self::whereHas('punchRules', function ($query) {
            $query->where('punch_type_id', PunchRules::NORMALWORK);
        })
            ->where(['year' => $year, 'month' => $month])
            ->count();
    }
}