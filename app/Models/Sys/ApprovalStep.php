<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/8
 * Time: 20:04
 * 审核流程配置
 */

namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ApprovalStep extends Model
{
    use LogsActivity;

    protected $table = 'approval_step';

    protected $primaryKey = 'step_id';

    //时间范围
    const RANGE_TYPE_1 = 1;
    const RANGE_TYPE_2 = 2;
    const RANGE_TYPE_3 = 3;
    const RANGE_TYPE_4 = 4;

    public static $timeType = [
        self::RANGE_TYPE_1 => '0天(补打卡)',
        self::RANGE_TYPE_2 => '半天到一天(大于0天小于等于1天)',
        self::RANGE_TYPE_3 => '1天半到3天(大于1天小于等于3天)',
        self::RANGE_TYPE_4 => '3天以上'
    ];

    public static $timeRange = [
        self::RANGE_TYPE_1 => ['min' => 0 , 'max' => 0],
        self::RANGE_TYPE_2 => ['min' => 0.5 , 'max' => 1],
        self::RANGE_TYPE_3 => ['min' => 1.5 , 'max' => 3],
        self::RANGE_TYPE_4 => ['min' => 3.5 , 'max' => 365],
    ];

    protected $fillable = [
        'dept_id',
        'step',
        'time_range_id',
    ];

}