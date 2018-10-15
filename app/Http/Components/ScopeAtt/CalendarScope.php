<?php
/**
 * Created by PhpStorm.
 * User: wangyingjie
 * Date: 2018/10/10
 * Time: 14:32
 */

namespace App\Http\Components\ScopeAtt;


use App\Components\Helper\GeneralScope;

class CalendarScope extends GeneralScope
{
    public $displayLastMonth = false;

    public function __construct(array $params, $user)
    {
        parent::__construct($params, $user);
        $this->endDate = isset($params['endDate']) ? date('Y-m-t', is_numeric($params['endDate']) ? $params['endDate'] / 1000 : strtotime($params['endDate'])) : date('Y-m-t',time());
        $this->startDate = isset($params['startDate']) ? date('Y-m-t', is_numeric($params['startDate']) ? $params['startDate'] / 1000 : strtotime($params['startDate'])) : date('Y-m-t',time());
    }
}