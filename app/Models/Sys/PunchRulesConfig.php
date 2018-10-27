<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/25
 * Time: 12:02
 */

namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PunchRulesConfig extends Model
{
    use LogsActivity;

    protected $table = 'punch_rules_config';

    protected $primaryKey = 'id';

    protected $fillable = [
        'punch_rules_id',
        'ready_time',
        'work_start_time',
        'work_end_time',
        'rule_desc',
        'late_type',
        'start_gap',
        'end_gap',
        'ded_type',
        'holiday_id',
        'ded_num',
    ];

}