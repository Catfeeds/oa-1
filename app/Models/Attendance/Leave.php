<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 14:44
 */
namespace App\Models\Attendance;

use App\Models\Sys\HolidayConfig;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
{
    use LogsActivity;

    protected $table = 'users_leave';

    protected $primaryKey = 'leave_id';

    const HASNOTIME = '1999-01-01';

    const MY_LEAVE = 1;
    const DEPT_LEAVE = 2;
    const COPY_LEAVE = 3;

    const LOGIN_INFO = 1;
    const LOGIN_VERIFY_INFO = 2;

    const WAIT_REVIEW = 0;
    const ON_REVIEW = 1;
    const REFUSE_REVIEW = 2;
    const PASS_REVIEW = 3;
    const CANCEL_REVIEW = 4;

    public static $types = [
        self::MY_LEAVE => '我的申请单',
        self::DEPT_LEAVE => '部门加班调休单',
        self::COPY_LEAVE => '抄送我的申请单',
    ];

    public static $startId = [
        1 => '09:00',
        2 => '13:45',
        3 => '19:00',
        0 => ''
    ];

    public static $endId = [
        1 => '12:00',
        2 => '18:00',
        3 => '20:00',
        0 => ''
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
        4 => '已取消',
    ];

    protected $fillable = [
        'user_id',
        'step_id',
        'holiday_id',
        'start_time',
        'start_id',
        'end_time',
        'end_id',
        'number_day',
        'reason',
        'status',
        'user_list',
        'annex',
        'review_user_id',
        'remain_user',
        'copy_user',
    ];

    public function holidayConfig() {
        return $this->hasOne(HolidayConfig::class, 'holiday_id', 'holiday_id');
    }

    public static function getHolidayIdList()
    {
        return self::get(['holiday_id', 'leave_id'])->pluck('holiday_id', 'leave_id')->toArray();
    }

}