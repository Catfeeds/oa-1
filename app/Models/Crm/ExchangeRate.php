<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 14:44
 */

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ExchangeRate extends Model
{
    use LogsActivity;

    protected $table = 'cmr_exchange_rate';

    protected $primaryKey = 'id';

    const UNEDITED = 1;
    const EDITED = 2;
    const TYPE = [
        self::UNEDITED => '未编辑',
        self::EDITED => '已编辑'
    ];

    protected $fillable = [
        'billing_cycle',
        'currency',
        'exchange_rate',
        'type'
    ];

    public static function getList($cycle)
    {
        $tmp = self::where(['billing_cycle' => $cycle])->get()->toArray();
        $tmp1 = [];
        foreach ($tmp as $v) {
            if ($v['type'] == 1){
                return false;
            }
            $tmp1[$v['currency']] = $v['exchange_rate'];
        }
        return $tmp1;
    }

}