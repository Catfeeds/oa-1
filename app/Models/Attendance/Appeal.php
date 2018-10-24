<?php

namespace App\Models\Attendance;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Appeal extends Model
{
    protected $table = "appeal";
    protected $fillable = [
        'apply_type_id', 'user_id', 'reason', 'result', 'remark', 'operate_user_id', 'leave_id', 'appeal_type', 'daily_id'
    ];
    public $primaryKey = "id";

    const APPEAL_LEAVE = 1;
    const APPEAL_DAILY = 2;

    public static $appealType = [
        self::APPEAL_LEAVE => '请假申诉',
        self::APPEAL_DAILY => '每日考勤申诉',
    ];

    public static function getAppealResult($appealType)
    {
        $columns = [
            self::APPEAL_LEAVE => 'leave_id',
            self::APPEAL_DAILY => 'daily_id',
        ];

        return self::where(['user_id' => \Auth::user()->user_id, 'appeal_type' => $appealType])
            ->get()->pluck('result', $columns[$appealType])->toArray();
    }

    public static function getTextArr()
    {
        return [
            '0' => '已申诉,等待审核',
            '1' => '申诉通过',
            '2' => '申诉失败'
        ];
    }

    public function users()
    {
        return $this->hasOne(User::class, 'user_id', 'user_id');
    }

    public function daily()
    {
        return $this->hasOne(DailyDetail::class, 'id', 'daily_id');
    }
}
