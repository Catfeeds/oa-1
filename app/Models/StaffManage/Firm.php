<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/14
 * Time: 9:53
 * 公司配置数据库模型
 */

namespace App\Models\StaffManage;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Firm extends Model
{
    use LogsActivity;

    protected $table = 'firm';

    protected $primaryKey = 'firm_id';

    protected $fillable = [
        'firm',
        'alias',
    ];

    public static function getFirmList()
    {
        return self::get(['firm_id', 'firm'])->pluck('firm', 'firm_id')->toArray();
    }
}