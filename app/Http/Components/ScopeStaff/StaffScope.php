<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/18
 * Time: 20:17
 */

namespace App\Http\Components\ScopeStaff;

use App\Components\Helper\GeneralScope;

class StaffScope extends GeneralScope
{
    public $userId;
    public $deptId;
    public $sex;
    public $statusId;
    public $displayLastMonth = false;
    public $showDate = true;
    public $defaultDateRange = 3600;

    public function __construct(array $params, $user = null)
    {

        $this->userId = $params['user_id'] ?? NULL;
        $this->deptId = $params['dept_id'] ?? NULL;
        $this->sex = $params['sex'] ?? NULL;
        $this->statusId = $params['status'] ?? NULL;


        parent::__construct($params, $user);
    }

    public function setWhere($tableAlias = null)
    {

        $where = [
            $this->getDateWhere($tableAlias, 'entry_time'),
        ];

        if(!empty($this->userId)) {
            $where[] = sprintf("users.user_id = %d", $this->userId);
        }

        if(!empty($this->sex)) {
            $where[] = sprintf("users_ext.sex = %d", $this->sex);
        }
        if(!empty($this->deptId)) {
            $where[] = sprintf("users.dept_id = %d", $this->deptId);
        }

        if (!is_null($this->statusId)){
            $where[] = sprintf("users.status = %d", $this->statusId);
        }

        $this->where = empty($where) ? '1 = 1' : implode(' AND ', $where);
    }
}