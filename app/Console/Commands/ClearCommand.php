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
        if ($this->confirm('你确定要清档吗？')) {
            //不删除表
            $unDelTable = ['activity_log', 'migrations', 'permission_role', 'permissions', 'role_user', 'roles'];
            //不删除表列表：以xxx开头的表
            $unDelTableList = ['cmr_', 'sys_'];


            $tableInDb = \DB::select('show tables');
            $delTable = [];
            $db = "Tables_in_" . env('DB_DATABASE');
            foreach ($tableInDb as $tab) {
                $table = $tab->{$db};
                if (in_array($table, $unDelTable)) continue;
                if (in_array(substr($table, 0, 4), $unDelTableList)) continue;

                $delTable[] = $tab->{$db};
            }

            //关闭外键限制
            \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            foreach ($delTable as $del) {
                \DB::table($del)->truncate();
            }
            \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            $this->info2('清档成功');
        } else {
            $this->info2('放弃档');
        }
    }
}
