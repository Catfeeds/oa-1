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
        '_staff',
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
            'role-all' => '账号体系模块/职务/「所有」',
            'role' => '账号体系模块/职务/「列表」',
            'role.create' => '账号体系模块/职务/「添加」',
            'role.edit' => '账号体系模块/职务/「设置」',
            'role.appoint' => '账号体系模块/职务/「指派」',
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

            'holiday-config-all' => '系统配置/假期管理/「所有」',
            'holiday-config' => '系统配置/假期管理/「列表」',
            'holiday-config.create' => '系统配置/假期管理/「添加」',
            'holiday-config.edit' => '系统配置/假期管理/「设置」',

            'approval-step-all' => '系统配置/审核流程管理/「所有」',
            'approval-step' => '系统配置/审核流程管理/「列表」',
            'approval-step.create' => '系统配置/审核流程管理/「添加」',
            'approval-step.edit' => '系统配置/审核流程管理/「设置」',

            'punch-rules-all' => '系统配置/上下班时间管理/「所有」',
            'punch-rules' => '系统配置/上下班时间管理/「列表」',
            'punch-rules.create' => '系统配置/上下班时间管理/「添加」',
            'punch-rules.edit' => '系统配置/上下班时间管理/「设置」',

            'calendar-all' => '系统配置/日历表管理/「所有」',
            'calendar' => '系统配置/日历表管理/「列表」',
            'calendar.create' => '系统配置/日历表管理/「添加」',
            'calendar.edit' => '系统配置/日历表管理/「设置」',

        ];
    }

    // 考勤功能
    private function _attendance(): array
    {
        return [
            'attendance-all' => '考勤功能/考勤功能管理「所有」',

            'leave-all' => '考勤功能/假期管理/「所有」',
            'leave' => '考勤功能/假期管理/我的假期/ 「列表」',
            'leave.create' => '考勤功能/假期管理/我的假期/「添加」',
            'leave.edit' => '考勤功能/假期管理/我的假期/「编辑」',
            'leave.review' => '考勤功能/假期管理/假期审核/「审核」',

            'daily-detail-all' => '考勤功能/每日考勤管理/「所有」',
            'daily-detail-notice' => '考勤功能/每日考勤管理/「通知」',
            'daily-detail' => '考勤功能/每日考勤管理/我的明细/ 「列表」',

            'daily-detail.edit' => '考勤功能/每日考勤管理/我的明细/「管理员编辑」',
            'daily-detail.review' => '考勤功能/每日考勤管理/明细管理/「管理员操作」',

            'daily-detail.review.detail' => '考勤功能/考勤管理/「查看明细」',
            'daily-detail.review.send' => '考勤功能/考勤管理/「发布考勤通知」',
            'daily-detail.review.send-batch' => '考勤功能/考勤管理/「批量发布考勤通知」',
            'daily-detail.review.export' => '考勤功能/考勤管理/「选择导出excel」',
            'daily-detail.review.export-batch' => '考勤功能/考勤管理/「批量导出excel」',
        ];
    }

    // 员工管理
    private function _staff(): array
    {
        return [
            'staff-all' => '员工管理/员工列表/「所有」',
            'staff' => '员工管理/员工列表/「列表」',
            'staff.edit' => '员工管理/员工列表/「编辑」',
            'staff.info' => '员工管理/员工列表/「查看」',

            'entry' => '员工管理/员工入职/「列表」',
            'entry.create' => '员工管理/员工入职/「添加」',
            'entry.edit' => '员工管理/员工入职/「编辑」',
            'entry.del' => '员工管理/员工入职/「删除」',
            'entry.sendMail' => '员工管理/员工入职/「发送入职信息填写」',
            'entry.review' => '员工管理/员工入职/「入职审核」',

            'firm-all' => '员工管理/公司配置/「所有」',
            'firm' => '员工管理/公司配置/「列表」',
            'firm.create' => '员工管理/公司配置/「添加」',
            'firm.edit' => '员工管理/公司配置/「编辑」',

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
            'reconciliation-reconciliationAudit.invoice' => 'CRM功能/对账功能/对账审核/「开票确认」',
            'reconciliation-reconciliationAudit.payback' => 'CRM功能/对账功能/对账审核/「回款确认」',
            'reconciliation-reconciliationAudit.revision' => 'CRM功能/对账功能/对账审核/「调整流水」',

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

            'reconciliation-reconciliationExchangeRate' => 'CRM功能/对账功能/货币汇率管理/「所有」',
            'reconciliation-reconciliationExchangeRate.create' => 'CRM功能/对账功能/货币汇率管理/「添加」',
            'reconciliation-reconciliationExchangeRate.edit' => 'CRM功能/对账功能/货币汇率管理/「编辑」',
            'reconciliation-reconciliationExchangeRate.conversion' => 'CRM功能/对账功能/货币汇率管理/「转化」',

            'reconciliation-reconciliationPool' => 'CRM功能/对账功能/分成比例汇总/「所有」',
            'reconciliation-reconciliationPool.detail' => 'CRM功能/对账功能/分成比例汇总/「明细」',

            'reconciliation-reconciliationSchedule' => 'CRM功能/对账功能/对账进度跟踪/「所有」',
        ];
    }


}
