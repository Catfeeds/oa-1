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

class LeaveScope extends GeneralScope
{
    public $holidayId;
    public $displayLastMonth = false;

    public function __construct(array $params, $user = null)
    {

        $this->holidayId = $params['holiday_id'] ?? '';
        parent::__construct($params, $user);
    }

    public function setWhere($tableAlias = null)
    {
        if ($tableAlias !== null) {
            $tableAlias = sprintf('%s.', $tableAlias);
        }

        $where = [
            $this->getDateWhere($tableAlias, 'start_time'),
        ];

        if(!empty($this->holidayId)) {
            $where[] = sprintf("holiday_id = %d", $this->holidayId);
        }

        $this->where = empty($where) ? '1 = 1' : implode(' AND ', $where);
    }
}