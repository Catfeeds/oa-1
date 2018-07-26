<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;

class Permissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public $model = [
        '_base',
    ];

    public function run()
    {
        $perms = [];
        foreach ($this->model as $v) {
            if (empty(array_intersect($perms, $this->$v()))) {
                $perms = array_merge(
                    $perms,
                    $this->$v()
                );
            } else {
                exit('出现重复的权限命名,模块名为:' . $v);
            }
        }

        foreach ($perms as $name => $display_name) {
            $description = $display_name;
            $p = Permission::firstOrCreate(compact('name'));
            $p->update(compact('display_name', 'description'));
        }
    }

    // 基本
    private function _base(): array
    {
        return [
            'user-all' => '账号体系模块/账号/「所有」',
            'user' => '账号体系模块/账号/「列表」',
            'user.create' => '账号体系模块/账号/「添加」',
            'user.edit' => '账号体系模块/账号/「设置」',
            'role-all' => '账号体系模块/角色/「所有」',
            'role' => '账号体系模块/角色/「列表」',
            'role.create' => '账号体系模块/角色/「添加」',
            'role.edit' => '账号体系模块/角色/「设置」',
            'role.appoint' => '账号体系模块/角色/「指派」',
            'profile.password' => '账号体系模块/「密码重置」',

            'version-all' => '账号体系模块/版本管理/「所有」',
            'version' => '账号体系模块/版本管理/「列表」',
            'version.create' => '账号体系模块/版本管理/「添加」',
            'version.edit' => '账号体系模块/版本管理/「设置」',

            'stat-cron-all' => '账号体系模块/任务计划列表/「所有」',
            'stat-cron' => '账号体系模块/任务计划列表/「列表」',
            'stat-cron.create' => '账号体系模块/任务计划列表/「添加」',
            'stat-cron.edit' => '账号体系模块/任务计划列表/「设置」',
        ];
    }

}
