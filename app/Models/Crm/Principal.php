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

class Principal extends Model
{
    use LogsActivity;

    protected $table = 'cmr_reconciliation_principal';

    protected $primaryKey = 'id';

    const OPS = 1;
    const OPD = 2;
    const FAC = 3;
    const TREASURER = 4;
    const FRC = 5;
    const FSR = 6;

    const JOB = [
        self::OPS => 'ops',
        self::OPD => 'opd',
        self::FAC => 'fac',
        self::TREASURER => 'treasurer',
        self::FRC => 'frc',
        self::FSR => 'fsr',
    ];

    protected $fillable = [
        'product_id',
        'job_id',
        'principal_id',
    ];

}