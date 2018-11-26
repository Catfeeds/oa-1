<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/14
 * Time: 9:54
 * 员工入职数据库模型
 */
namespace App\Models\StaffManage;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Entry extends Model
{
    use LogsActivity;

    protected $table = 'entry';

    protected $primaryKey = 'entry_id';

    //创建可填写工作经历和家庭成员表格数量
    const CREATE_WORK_HISTORY_NUM = 2;
    const CREATE_FAMILY_NUM = 2;

    const WAIT_SEND = 0;
    const HAS_SEND = 1;
    const FILL_IN = 2;
    const FILL_END = 3;
    const REVIEW_PASS = 4;
    const REVIEW_REFUSE = 5;

    public static $status = [
        self::WAIT_SEND => '待发送',
        self::HAS_SEND => '已发送,待填写资料',
        self::FILL_IN => '填写资料中',
        self::FILL_END => '填写完成',
        self::REVIEW_PASS => '通过入职',
        self::REVIEW_REFUSE => '放弃入职',
    ];


    const GREGORIAN_CALENDAR = 0;
    const LUNAR_CALENDAR = 1;
    public static $nature = [
        1 => '全职',
        2 => '兼职',
        3 => '临时工',
    ];

    public static $hireTYpe = [
        1 => '社招',
        2 => '校招',
    ];

    public static $birthdayType = [
        self::GREGORIAN_CALENDAR => '公历',
        self::LUNAR_CALENDAR => '农历',
    ];

    protected $fillable = [
        'name',
        'sex',
        'mobile',
        'email',
        'entry_time',
        'nature_id',
        'hire_id',
        'firm_id',
        'dept_id',
        'job_id',
        'job_name',
        'leader_id',
        'tutor_id',
        'friend_id',
        'place',
        'copy_user',
        'status',
        'creater_id',
        'review_id',
        'remember_token',
        'send_time',
        'card_id',
        'card_address',
        'ethnic',
        'birthplace',
        'political',
        'census',
        'family_num',
        'marital_status',
        'blood_type',
        'genus_id',
        'constellation_id',
        'height',
        'weight',
        'qq',
        'live_address',
        'urgent_name',
        'urgent_bind',
        'urgent_tel',
        'education_id',
        'graduation_time',
        'specialty',
        'degree',

        'birthday',
        'salary_card',
        'family_num',
        'work_history',
        'project_empiric',
        'awards',
        'used_email',
        'birthday_type',
        'firm_call',


    ];


    public static function getSchoolList()
    {
        return self::get(['school_id', 'school'])->pluck('school', 'school_id')->toArray();
    }
}