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
        '_attendanceCfg',
        '_crm',
        '_staff',
        '_staffCfg',
        '_oa',
        '_bulletin'
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
            $arr = explode('/', $display_name);
            $description = array_shift($arr);
            $display_name = end($arr);
            $p = Permission::firstOrCreate(compact('name'));
            $p->update(compact('display_name', 'description'));
        }
    }

    /**
     * OA 系统所有权限
     * @return array
     */
    private function _oa():array
    {
        return [

            'oa-all' => '所有权限/所有权限'
        ];
    }

    // 基本
    private function _base(): array
    {
        return [
            'user-all'          => '所有权限/账号管理',
            'user'              => '账号管理/账号列表',
            'user.edit'         => '账号管理/账号编辑',
            'profile.password'  => '账号管理/密码重置',

            'role-all'          => '所有权限/权限管理',
            'role'              => '权限管理/权限列表',
            'role.create'       => '权限管理/权限添加',
            'role.edit'         => '权限管理/权限编辑',
            'role.appoint'      => '权限管理/权限指派',
        ];
    }


    // 考勤功能
    private function _attendance(): array
    {
        return [

            'attendance-all'                    => '所有权限/考勤管理',

            'leave-all'                         => '考勤管理/申请单管理',
            'leave'                             => '申请单管理/申请单列表',
            'leave.create'                      => '申请单管理/申请单添加',
            'leave.edit'                        => '申请单管理/申请单编辑',
            'leave.review'                      => '申请单管理/申请单审核',

            'daily-detail-all'                  => '考勤管理/员工明细管理',
            'daily-detail'                      => '员工明细管理/考勤明细列表',
            'daily-detail.confirm'              => '员工明细管理/月度考勤确认',
            'daily-detail.edit'                 => '员工明细管理/员工考勤编辑',

            'daily-detail-review-all'           => '考勤管理/员工考勤管理',
            'daily-detail.review'               => '员工考勤管理/员工考勤列表',
            'daily-detail.review.import'        => '员工考勤管理/员工考勤信息导入',
            'daily-detail.review.edit'          => '员工考勤管理/员工考勤信息编辑',
            'daily-detail.review.create'        => '员工考勤管理/员工考勤信息生成',
            'daily-detail.review.detail'        => '员工考勤管理/员工考勤明细',
            'daily-detail.review.send'          => '员工考勤管理/发布考勤通知',
            'daily-detail.review.send-batch'    => '员工考勤管理/批量发布考勤通知',
            'daily-detail.review.export'        => '员工考勤管理/选择导出excel',
            'daily-detail.review.export-batch'  => '员工考勤管理/批量导出excel',

            'appeal-all'                        => '考勤管理/员工申诉管理',
            'appeal.store'                      => '员工申诉管理/员工添加申诉',
            'appeal.update'                     => '员工申诉管理/管理员审核申诉',
            'appeal.review'                     => '员工申诉管理/申诉列表',
        ];
    }

    private function _attendanceCfg(): array
    {
        return [
            'attendance-cfg-all'        => '所有权限/考勤信息配置管理',

            'holiday-config-all'        => '考勤信息配置管理/假期信息管理',
            'holiday-config'            => '假期信息管理/假期信息列表',
            'holiday-config.create'     => '假期信息管理/假期信息添加',
            'holiday-config.edit'       => '假期信息管理/假期信息编辑',

            'approval-step-all'         => '考勤信息配置管理/审核流程信息管理',
            'approval-step'             => '审核流程信息管理/审核流程信息列表',
            'approval-step.create'      => '审核流程信息管理/审核流程信息添加',
            'approval-step.edit'        => '审核流程信息管理/审核流程信息编辑',

            'punch-rules-all'           => '考勤信息配置管理/上下班时间信息管理',
            'punch-rules'               => '上下班时间信息管理/上下班时间信息列表',
            'punch-rules.create'        => '上下班时间信息管理/上下班时间信息添加',
            'punch-rules.edit'          => '上下班时间信息管理/上下班时间信息编辑',

            'calendar-all'              => '考勤信息配置管理/日历表信息管理',
            'calendar'                  => '日历表信息管理/日历表信息列表',
            'calendar.create'           => '日历表信息管理/日历表信息添加',
            'calendar.edit'             => '日历表信息管理/日历表信息编辑',

        ];
    }




    // 员工管理
    private function _staff(): array
    {
        return [

            'staff-all'        => '所有权限/员工管理',

            'manage.index'     => '员工管理/员工工作台',
            'staff'            => '员工管理/员工列表',
            'staff.edit'       => '员工管理/员工编辑',
            'staff.info'       => '员工管理/员工查看',
            'staff.export'     => '员工管理/员工信息导出',

            'entry-all'        => '员工管理/员工入职管理',
            'entry'            => '员工入职管理/入职信息列表',
            'entry.create'     => '员工入职管理/入职信息添加',
            'entry.edit'       => '员工入职管理/入职信息编辑',
            'entry.del'        => '员工入职管理/入职信息删除',
            'entry.sendMail'   => '员工入职管理/入职信息发送填写',
            'entry.review'     => '员工入职管理/入职审核',
        ];
    }
    // 员工管理 配置
    private function _staffCfg():array
    {
        return [
            'staff-cfg-all'    => '所有权限/员工信息配置管理',

            'firm-all'         => '员工信息配置管理/公司配置管理',
            'firm'             => '公司配置管理/公司配置列表',
            'firm.create'      => '公司配置管理/公司配置添加',
            'firm.edit'        => '公司配置管理/公司配置编辑',

            'school-all'       => '员工信息配置管理/学校配置管理',
            'school'           => '学校配置管理/学习配置列表',
            'school.create'    => '学校配置管理/学习配置添加',
            'school.edit'      => '学校配置管理/学习配置编辑',

            'dept-all'         => '员工信息配置管理/部门配置管理',
            'dept'             => '部门配置管理/部门配置列表',
            'dept.create'      => '部门配置管理/部门配置添加',
            'dept.edit'        => '部门配置管理/部门配置编辑',

            'job-all'          => '员工信息配置管理/岗位类型配置管理',
            'job'              => '岗位类型配置管理/岗位类型配置列表',
            'job.create'       => '岗位类型配置管理/岗位类型配置添加',
            'job.edit'         => '岗位类型配置管理/岗位类型配置编辑',

        ];
    }


    // 对账功能
    private function _crm(): array
    {
        return [
            'crm-all' => '所有权限/CRM管理',

            'reconciliation-all' => 'CRM管理/对账管理',
            'reconciliation-reconciliationAudit.global'                    => '对账管理/全局查看权限',
            'reconciliation-reconciliationAudit'                    => '对账管理/对账列表',
            'reconciliation-reconciliationAudit.edit'               => '对账管理/对账编辑',
            'reconciliation-reconciliationAudit.review'             => '对账管理/对账审核',
            'reconciliation-reconciliationAudit.download'           => '对账管理/对账导出',
            'reconciliation-reconciliationAudit.invoice'            => '对账管理/对账开票确认',
            'reconciliation-reconciliationAudit.payback'            => '对账管理/对账回款确认',
            'reconciliation-reconciliationAudit.revision'           => '对账管理/对账调整流水',
            'reconciliationAudit.notice'                            => '对账管理/一键通知',

            'reconciliation-reconciliationPrincipal-all'            => 'CRM管理/负责人管理',
            'reconciliation-reconciliationPrincipal'                => '负责人管理/负责人信息列表',
            'reconciliation-reconciliationPrincipal.edit'           => '负责人管理/负责人信息编辑',

            'reconciliation-reconciliationDifferenceType-all'       => 'CRM管理/对账差异类管理',
            'reconciliation-reconciliationDifferenceType'           => '对账差异类管理/对账差异类信息列表',
            'reconciliation-reconciliationDifferenceType.create'    => '对账差异类管理/对账差异类信息添加',
            'reconciliation-reconciliationDifferenceType.edit'      => '对账差异类管理/对账差异类信息编辑',

            'reconciliation-reconciliationProportion-all'           => 'CRM管理/分成比例管理',
            'reconciliation-reconciliationProportion'               => '分成比例管理/分成比例信息列表',
            'reconciliation-reconciliationProportion.edit'          => '分成比例管理/分成比例信息编辑',


            'reconciliation-reconciliationExchangeRate-all'         => 'CRM管理/货币汇率管理',
            'reconciliation-reconciliationExchangeRate'             => '货币汇率管理/货币汇率信息列表',
            'reconciliation-reconciliationExchangeRate.create'      => '货币汇率管理/货币汇率信息添加',
            'reconciliation-reconciliationExchangeRate.edit'        => '货币汇率管理/货币汇率信息编辑',
            'reconciliation-reconciliationExchangeRate.conversion'  => '货币汇率管理/货币汇率信息转化',

            'reconciliation-reconciliationPool-all'                 => 'CRM管理/分成比例汇总管理',
            'reconciliation-reconciliationPool'                     => '分成比例汇总管理/分成比例汇总信息列表',
            'reconciliation-reconciliationPool.detail'              => '分成比例汇总管理/分成比例汇总信息明细',

            'reconciliation-reconciliationSchedule-all'             => 'CRM管理/对账进度跟踪管理',
            'reconciliation-reconciliationSchedule'                 => '对账进度跟踪管理/对账进度跟踪信息列表',
        ];
    }

    //公告栏管理
    private function _bulletin(): array
    {
        return [
            'bulletin-all'    => '所有权限/公告栏管理',

            'bulletin.index'  => '公告栏管理/公告配置列表',
            'bulletin.create' => '公告栏管理/公告添加',
            'bulletin.edit'   => '公告栏管理/公告修改',
        ];
    }
}
