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

class Reconciliation extends Model
{
    use LogsActivity;

    protected $table = 'cmr_reconciliation';

    protected $primaryKey = 'id';

    const UNRD = 1;
    const OPS = 2;
    const OPD = 3;
    const FAC = 4;
    const TREASURER = 5;
    const FRC = 6;
    const FSR = 7;
    const COMPLETE = 8;
    const REVIEW_TYPE = [
        self::UNRD => '未审核',
        self::OPS => '运营专员审核',
        self::OPD => '运营主管审核',
        self::FAC => '财务计提专员审核',
        self::TREASURER => '财务计提主管审核',
        self::FRC => '财务对账专员审核',
        self::FSR => '财务对账主管审核',
        self::COMPLETE => '审核完成',
    ];

    const OPERATION = 1;
    const ACCRUAL = 2;
    const RECONCILIATION = 3;
    const ALL = 4;
    const REVIEW = [
        self::OPERATION => '运营审核',
        self::ACCRUAL => '计提审核',
        self::RECONCILIATION => '对账审核',
        self::ALL => '审核结果',
    ];

    protected $fillable = [
        'id',
        'product_id',
        'billing_cycle',
        'billing_cycle_start',
        'income_type',
        'billing_cycle_end',
        'company',
        'client',
        'game_name',
        'online_name',
        'business_line',
        'area',
        'reconciliation_currency',
        'os',
        'divided_type',
        'backstage_channel',
        'unified_channel',
        'review_type',
        'backstage_water_other',
        'backstage_water_rmb',
        'operation_adjustment',
        'operation_type',
        'operation_remark',
        'operation_user_name',
        'operation_time',
        'operation_water_other',
        'operation_water_rmb',
        'accrual_adjustment',
        'accrual_type',
        'accrual_remark',
        'accrual_user_name',
        'accrual_time',
        'accrual_water_other',
        'accrual_water_rmb',
        'reconciliation_adjustment',
        'reconciliation_type',
        'reconciliation_remark',
        'reconciliation_user_name',
        'reconciliation_time',
        'reconciliation_water_other',
        'reconciliation_water_rmb',
    ];

}