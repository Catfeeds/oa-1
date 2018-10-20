<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/7
 * Time: 16:54
 * 假期配置表数据库
 */

namespace App\Models\Sys;

use App\Http\Components\Helpers\AttendanceHelper;
use App\Models\UserExt;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class HolidayConfig extends Model
{
    use LogsActivity;

    protected $table = 'users_holiday_config';

    protected $primaryKey = 'holiday_id';

    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;

    const LEAVEID = 1;
    const CHANGE = 2;
    const OVERTIME = 2;
    const RECHECK = 3;

    const NO_SETTING = 0;
    const GO_WORK = 1;
    const OFF_WORK = 2;

    const OVER_TIME = 1;
    const WEEK_WORK = 2;
    const WORK_CHANGE = 3;

    const YEAR_RESET = 1;
    const MONTH_RESET = 2;

    const RELIEF_GO_WORK = 1;
    const RELIEF_OFF_WORK = 2;

    const CYPHER_NO_RESTRICT = -1;
    const CYPHER_UNPAID = 1;
    const CYPHER_PAID = 2;
    const CYPHER_CHANGE = 3;
    const CYPHER_OVERTIME = 4;
    const CYPHER_RECHECK = 5;
    const CYPHER_HOUR = 6;

    const RESET_ENTRY_TIME = 1;
    const RESET_NATURAL_CYCLE = 2;

    public static $resetType = [
        self::NO_SETTING => '不设置',
        self::RESET_ENTRY_TIME => '按入职时间',
        self::RESET_NATURAL_CYCLE => '按自然周期',
    ];

    public static $applyType = [
        self::LEAVEID => '请假',
        self::CHANGE => '加班调休',
        self::RECHECK => '补打卡',
    ];

    public static $driverType = [
        self::LEAVEID => 'leaved',
        self::CHANGE => 'change',
        self::RECHECK => 'recheck',
    ];

    public static $isShow = [
        self::STATUS_DISABLE => '否',
        self::STATUS_ENABLE => '是',

    ];

    public static $condition = [
        self::YEAR_RESET => '按年重置',
        self::MONTH_RESET => '按月重置'
    ];

    public static $punchType = [
        self::GO_WORK => '上班补卡',
        self::OFF_WORK => '下班补卡'
    ];

    public static $reliefType = [
        self::NO_SETTING => '不设置',
        self::RELIEF_GO_WORK => '上班',
        self::RELIEF_OFF_WORK => '下班'
    ];

    public static $changeType = [
        self::NO_SETTING => '不设置',
        self::OVER_TIME => '夜班加班调休',
        self::WEEK_WORK => '节假日加班',
        self::WORK_CHANGE => '调休'
    ];

    public static $cypherType = [
        self::CYPHER_NO_RESTRICT => '不限制',
        self::CYPHER_UNPAID => '无薪假',
        self::CYPHER_PAID => '带薪假',
        self::CYPHER_CHANGE => '调休假',
        self::CYPHER_OVERTIME => '加班',
        self::CYPHER_RECHECK => '打卡',
        self::CYPHER_HOUR => '小时假',
    ];

    public static $cypherTypeChar = [
        self::CYPHER_UNPAID => 'unpaid',
        self::CYPHER_PAID => 'paid',
        self::CYPHER_CHANGE => 'change',
        self::CYPHER_OVERTIME => 'overtime',
        self::CYPHER_RECHECK => 'recheck',
        self::CYPHER_HOUR => 'hour',
    ];

    protected $fillable = [
        'holiday',
        'change_type',
        'apply_type_id',
        'memo',
        'is_full',
        'sort',
        'is_annex',
        'condition_id',
        'restrict_sex',
        'punch_type',
        'show_name',
        'cypher_type',
        'work_relief_formula',
        'work_relief_type',
        'work_relief_cycle_num',
        'add_pop',
        'up_day',
        'under_day',
        'cycle_num',
        'payable',
        'payable_reset_formula',
        'payable_claim_formula',
        'payable_self_growth',
        'exceed_change_id',
        'is_show',
        'is_before_after',
        'reset_type',


        'num',
        'is_boon',
        'change_type',
    ];

    public static function getHolidayList()
    {
        return self::whereIn('restrict_sex', [\Auth::user()->userExt->sex, UserExt::SEX_NO_RESTRICT])
            ->where(['is_show' => self::STATUS_ENABLE])
            ->orderBy('sort', 'desc')
            ->get(['holiday_id', 'holiday'])
            ->pluck('holiday', 'holiday_id')
            ->toArray();
    }

    public static function getObjByName($name)
    {
        return self::where('holiday', 'like', "$name")->first() ?? NULL;
    }

    public static function getHolidayApplyList()
    {
        return self::get(['holiday_id', 'apply_type_id'])->pluck('apply_type_id', 'holiday_id')->toArray();
    }
}