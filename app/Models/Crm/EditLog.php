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

class EditLog extends Model
{
    use LogsActivity;

    protected $table = 'cmr_reconciliation_edit_logs';

    protected $primaryKey = 'id';
    /*阻止自动维护时间戳*/
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'billing_cycle_start',
        'billing_cycle_end',
        'client',
        'backstage_channel',
        'adjustment',
        'type',
        'remark',
        'user_name',
        'time',
    ];

}