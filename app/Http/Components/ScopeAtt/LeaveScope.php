<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/23
 * Time: 16:43
 * 我的假期搜索查询条件
 */

namespace App\Http\Components\ScopeAtt;

use App\Components\Helper\GeneralScope;
use App\Models\Sys\HolidayConfig;

class LeaveScope extends GeneralScope
{
    public $holidayId;
    public $statusId;
    public $displayLastMonth = false;

    public $daily;
    public $dailyUserId;
    public $dailyAlias;
    public $dailyDept;

    public function __construct(array $params, $user = null)
    {

        $this->holidayId = $params['holiday_id'] ?? '';
        $this->statusId = $params['status'] ?? NULL;
        $this->dailyUserId = $params['daily_user_id'] ?? '';
        $this->dailyAlias = $params['daily_alias'] ?? '';
        $this->dailyDept = $params['daily_dept'] ?? '';
        $this->daily = $params['daily'] ?? '';
        parent::__construct($params, $user);
    }

    public function setWhere($tableAlias = null)
    {
        if ($tableAlias !== null) {
            $tableAlias = sprintf('%s.', $tableAlias);
        }

        $where = [
            $this->getDateWhere($tableAlias, 'created_at'),
        ];

        if(!empty($this->holidayId)) {
            $where[] = sprintf("holiday_id = %d", $this->holidayId);
        }
        if (!is_null($this->statusId)){
            $where[] = sprintf("status = %d", $this->statusId);
        }

        if (isset($this->daily)){
            if (!empty($this->dailyUserId)){
                $where[] = sprintf("user_id = %d", $this->dailyUserId);
            }
            if (!empty($this->dailyAlias)){
                $where[] = sprintf("alias = '%s'", $this->dailyAlias);
            }
            if (!empty($this->dailyDept)){
                $where[] = sprintf("dept_id = %d", $this->dailyDept);
            }
            //当月考勤统计去掉默认的created_at字段的判断
            array_shift($where);
        }
        $this->where = empty($where) ? '1 = 1' : implode(' AND ', $where);
    }
}