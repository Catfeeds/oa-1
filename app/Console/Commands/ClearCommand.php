<?php

namespace App\Console\Commands;

use App\Console\Components\BaseCommand;
use Illuminate\Console\Command;

class ClearCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清档脚本';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle2()
    {
        $table = ['activity_log', 'approval_step', 'calendar', 'cmr_exchange_rate', 'cmr_product', 'cmr_reconciliation', 'cmr_reconciliation_difference_type', 'cmr_reconciliation_edit_logs', 'cmr_reconciliation_principal', 'cmr_reconciliation_proportion', 'migrations', 'permission_role', 'permissions', 'role_user', 'roles', 'users_dept', 'users_ethnic', 'users_holiday_config', 'users_job', 'users_school'];

        $tableInDb = \DB::select('show tables');
        $tables = [];
        $db = "Tables_in_".env('DB_DATABASE');
        foreach ($tableInDb as $tab) {
            $tables[] = $tab->{$db};
        }
        $delTable = array_diff($tables, $table);

        foreach ($delTable as $del) {
            //\DB::table($del)->truncate();
        }
        \Log::info('清档成功');
    }
}
