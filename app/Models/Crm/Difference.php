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

class Difference extends Model
{
    use LogsActivity;

    protected $table = 'cmr_reconciliation_difference_type';

    protected $primaryKey = 'id';

    protected $fillable = [
        'product_id',
        'type_name',
    ];

    public static function getList($productId)
    {
        return self::where(['product_id' => $productId])->get(['id','type_name'])->pluck('type_name', 'id')->toArray();
    }

}