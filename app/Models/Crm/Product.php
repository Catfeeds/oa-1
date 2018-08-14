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

class Product extends Model
{
    use LogsActivity;

    protected $table = 'cmr_product';

    protected $primaryKey = 'id';

    protected $fillable = [
        'product_id',
        'name',
    ];

    public static function getList($has = [])
    {
        if (!$has) {
            return self::get(['product_id', 'name'])->pluck('name', 'product_id')->toArray();
        } else {
            return self::whereIn('product_id', $has)->get(['product_id', 'name'])->pluck('name', 'product_id')->toArray();
        }
    }

}