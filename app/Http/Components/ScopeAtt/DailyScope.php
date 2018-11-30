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
    public $userId;
    public $status;
    public $dept;


    public function __construct(array $params, $user = null)
    {
        $this->userId = $params['user_id'] ?? '';
        $this->status = $params['status'] ?? NULL;
        $this->dept = $params['dept'] ?? '';

        parent::__construct($params, $user);
    }

    public function setWhere($tableAlias = null)
    {
        if (!empty($this->userId)){
            $where[] = sprintf("user_id = %d", $this->userId);
        }

        if (!empty($this->dept)){
            $where[] = sprintf("dept_id = %d", $this->dept);
        }
        $this->where = empty($where) ? '1 = 1' : implode(' AND ', $where);
    }
}