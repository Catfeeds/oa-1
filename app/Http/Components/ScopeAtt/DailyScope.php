<?php
/**
 * Created by PhpStorm.
 * User: wangyingjie
 * Date: 2018/9/6
 * Time: 14:57
 */

namespace App\Http\Components\ScopeAtt;

use App\Components\Helper\GeneralScope;

class DailyScope extends GeneralScope
{
    public $displayLastMonth = false;
    public $dailyUserId;
    public $dailyAlias;
    public $dailyDept;


    public function __construct(array $params, $user = null)
    {
        $this->dailyUserId = $params['daily_user_id'] ?? '';
        $this->dailyAlias = $params['daily_alias'] ?? '';
        $this->dailyDept = $params['daily_dept'] ?? '';

        parent::__construct($params, $user);
    }

    public function setWhere($tableAlias = null)
    {
        if (!empty($this->dailyUserId)){
            $where[] = sprintf("user_id = %d", $this->dailyUserId);
        }
        if (!empty($this->dailyAlias)){
            $where[] = sprintf("alias = '%s'", $this->dailyAlias);
        }
        if (!empty($this->dailyDept)){
            $where[] = sprintf("dept_id = %d", $this->dailyDept);
        }
        $this->where = empty($where) ? '1 = 1' : implode(' AND ', $where);
    }
}