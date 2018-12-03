<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;

class ConfirmAttendance extends Model
{
    const SEND = 0;
    const SENT = 1;
    const CONFIRM = 2;

    //根据confirm字段在页面上展示的文字是:
    //从管理员角度是:
    public static $stateAdmin = [
        self::SEND    => '待发送',
        self::SENT    => '已发送',
        self::CONFIRM => '已确认',
    ];
    //从用户角度是:
    public static $stateUser = [
        self::SEND    => '未出考勤单',
        self::SENT    => '已出考勤单,请确认',
        self::CONFIRM => '确认成功',
    ];

    public $table = "confirm_attendances";

    public $fillable = [
        'id', 'user_id', 'year', 'month', 'confirm',
    ];

    public static function getConfirmState($year, $month, $status)
    {
        return self::where(['year' => $year, 'month' => $month, 'confirm' => $status])->get()->pluck('confirm', 'user_id')->toArray();
    }

    public static function getConfirmList($year, $month)
    {
        return self::where(['year' => $year, 'month' => $month])->get()->pluck('confirm', 'user_id')->toArray();
    }
}
