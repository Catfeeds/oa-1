<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/28
 * Time: 10:09
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class UserHoliday extends Model
{
    use LogsActivity;

    protected $table = 'users_holiday';
    protected $primaryKey = 'id';

    // 没有 timestamps 相关字段
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'holiday_id',
        'num',
    ];

    public function holidayConfig(){
        return $this->belongsTo('\App\Models\Sys\HolidayConfig', 'holiday_id', 'holiday_id');
    }

}