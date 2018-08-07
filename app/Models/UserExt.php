<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/2
 * Time: 11:04
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class UserExt extends Model
{
    use LogsActivity;

    protected $table = 'users_ext';

    protected $primaryKey = 'users_ext_id';

    // 没有 timestamps 相关字段
    public $timestamps = false;

    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;

    //学历
    public static $education = [
        1 => '小学',
        2 => '高中',
        3 => '中专',
        4 => '大专',
        5 => '本科',
        6 => '博士',
    ];
    //星座
    public static $constellation = [
        1 => '白羊座',
        2 => '金牛座',
        3 => '双子座',
        4 => '巨蟹座',
        5 => '狮子座',
        6 => '处女座',
        7 => '天秤座',
        8 => '天蝎座',
        9 => '射手座',
        10 => '魔蝎座',
        11 => '水瓶座',
        12 => '双鱼座',
    ];
    //血型
    public static $blood = [
        1 => 'A型',
        2 => 'B型',
        3 => 'AB型',
        4 => 'O型'
    ];

    //性别
    public static $sex = [
        0 => '男',
        1 => '女',
    ];

    //婚姻
    public static $marital = [
        0 => '未婚',
        1 => '已婚',
    ];

    //是否公司挂靠

    public static $firmCall = [
        0 => '否',
        1 => '是',
    ];

    protected $fillable = [
        'school_id',
        'education_id',
        'graduation_time',
        'constellation_id',
        'blood_type',
        'entry_time',
        'turn_time',
        'incumbent_num',
        'contract_st',
        'contract_et',
        'contract_years',
        'contract_num',
        'sex',
        'age',
        'born',
        'birthplace',
        'marital_status',
        'family_num',
        'census',
        'card_id',
        'card_address',
        'qq',
        'live_address',
        'urgent_name',
        'urgent_tel',
        'salary_card',
        'is_confirm',
    ];

    public static function checkIsConfirm($userId)
    {
        $isConfirm = self::where(['user_id' => $userId])->first();
        return $isConfirm['is_confirm'] ?? 0;
    }

}