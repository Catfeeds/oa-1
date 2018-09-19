<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/18
 * Time: 19:53
 */
namespace App\Http\Components\ScopeStaff;

use App\Components\Helper\GeneralScope;

class EntryScope extends GeneralScope
{
    public $name;
    public $statusId;
    public $displayLastMonth = false;

    public function __construct(array $params, $user = null)
    {
        $this->name = $params['name'] ?? NULL;
        $this->statusId = $params['status'] ?? NULL;
        parent::__construct($params, $user);
    }

    public function setWhere($tableAlias = null)
    {
        if ($tableAlias !== null) {
            $tableAlias = sprintf('%s.', $tableAlias);
        }

        $where = [
            $this->getDateWhere($tableAlias, 'entry_time'),
        ];

        if(!empty($this->name)) {
            $where[] = sprintf("name like '%%%s%%'", $this->name);
        }
        if (!is_null($this->statusId)){
            $where[] = sprintf("status = %d", $this->statusId);
        }
        $this->where = empty($where) ? '1 = 1' : implode(' AND ', $where);

    }
}