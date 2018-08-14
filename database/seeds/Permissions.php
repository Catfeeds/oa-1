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
        '_attendance',
        '_crm',
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

            'dept-all' => '系统配置/部门管理/「所有」',
            'dept' => '系统配置/部门管理/「列表」',
            'dept.create' => '系统配置/部门管理/「添加」',
            'dept.edit' => '系统配置/部门管理/「设置」',

            'job-all' => '系统配置/岗位管理/「所有」',
            'job' => '系统配置/岗位管理/「列表」',
            'job.create' => '系统配置/岗位管理/「添加」',
            'job.edit' => '系统配置/岗位管理/「设置」',

            'school-all' => '系统配置/学校管理/「所有」',
            'school' => '系统配置/学校管理/「列表」',
            'school.create' => '系统配置/学校管理/「添加」',
            'school.edit' => '系统配置/学校管理/「设置」',

        ];
    }

    // 考勤功能
    private function _attendance(): array
    {
        return [
            'attendance-all' => '考勤功能/考勤管理/「所有」',

            'leave-all' => '考勤功能/考勤管理/我的假期「所有」',
            'leave' => '考勤功能/考勤管理/我的假期/ 「列表」',
            'leave.create' => '考勤功能/考勤管理/我的假期/「添加」',
            'leave.edit' => '考勤功能/考勤管理/我的假期/「编辑」',

        ];
    }

    // 对账功能
    private function _crm(): array
    {
        return [
            'crm-all' => 'CRM功能/「所有」',

            'reconciliation-all' => 'CRM功能/对账功能/「所有」',
            'reconciliation-reconciliationAudit' => 'CRM功能/对账功能/对账审核/「所有」',
            'reconciliation-reconciliationAudit.edit' => 'CRM功能/对账功能/对账审核/「编辑」',
            'reconciliation-reconciliationAudit.review' => 'CRM功能/对账功能/对账审核/「审核」',
            'reconciliation-reconciliationAudit.download' => 'CRM功能/对账功能/对账审核/「导出」',

            'reconciliation-reconciliationProduct' => 'CRM功能/对账功能/游戏列表/「所有」',
            'reconciliation-reconciliationProduct.create' => 'CRM功能/对账功能/游戏列表/「添加」',
            'reconciliation-reconciliationProduct.edit' => 'CRM功能/对账功能/游戏列表/「编辑」',

            'reconciliation-reconciliationPrincipal' => 'CRM功能/对账功能/负责人管理/「所有」',
            'reconciliation-reconciliationPrincipal.edit' => 'CRM功能/对账功能/负责人管理/「编辑」',

            'reconciliation-reconciliationDifferenceType' => 'CRM功能/对账功能/对账差异类管理/「所有」',
            'reconciliation-reconciliationDifferenceType.create' => 'CRM功能/对账功能/对账差异类管理/「添加」',
            'reconciliation-reconciliationDifferenceType.edit' => 'CRM功能/对账功能/对账差异类管理/「编辑」',

            'reconciliation-reconciliationProportion' => 'CRM功能/对账功能/分成比例管理/「所有」',
            'reconciliation-reconciliationProportion.edit' => 'CRM功能/对账功能/分成比例管理/「编辑」',
        ];
    }


}
