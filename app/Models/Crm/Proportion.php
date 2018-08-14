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

class Proportion extends Model
{
    use LogsActivity;

    protected $table = 'cmr_reconciliation_proportion';

    protected $primaryKey = 'id';

    protected $fillable = [
        'product_id',
        'billing_cycle',
        'client',
        'backstage_channel',
        'channel_rate',
        'first_division',
        'first_division_remark',
        'second_division',
        'second_division_remark',
        'second_division_condition',
        'user_name',
        'review_type',
    ];

}