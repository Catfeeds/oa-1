<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 14:44
 */
namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
{
    use LogsActivity;

    protected $table = 'users_leave';

    protected $primaryKey = 'leave_id';

    public static $startId = [
        1 => '09:00',
        2 => '13:45',
        3 => '19:00',
    ];

    public static $endId = [
        1 => '12:00',
        2 => '18:00',
        3 => '20:00',
    ];

    /**
     * 审核状态
     * @var array
     */
    public static $status = [
        0 => '待审核',
        1 => '审核中',
        2 => '已拒绝',
        3 => '已通过',
    ];

    protected $fillable = [
        'user_id',
        'step_id',
        'holiday_id',
        'start_time',
        'start_id',
        'end_time',
        'end_id',
        'reason',
        'status',
        'user_list',
        'annex',
        'review_user_id',
        'remain_user'
    ];

}