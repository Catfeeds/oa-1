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
    public $applyTypeId;
    public $userId;
    public $displayLastMonth = false;

    public function __construct(array $params, $user = null)
    {
        $this->userId = $params['user_id'] ?? '';
        $this->holidayId = $params['holiday_id'] ?? '';
        $this->applyTypeId = $params['apply_type_id'] ?? '';

        $this->statusId = $params['status'] ?? NULL;
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

        if (!empty($this->userId)){
            $where[] = sprintf("user_id = %d", $this->userId);
        }

        if(!empty($this->applyTypeId)) {
            $where[] = sprintf("holiday_id in (select holiday_id from sys_attendance_holiday_config where apply_type_id = %d)", $this->applyTypeId);
        }

        if(!empty($this->holidayId)) {
            $where[] = sprintf("holiday_id = %d", $this->holidayId);
        }

        if (!is_null($this->statusId)){
            $where[] = sprintf("status = %d", $this->statusId);
        }

        $this->where = empty($where) ? '1 = 1' : implode(' AND ', $where);
    }
}