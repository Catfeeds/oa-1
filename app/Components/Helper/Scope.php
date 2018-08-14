<?php

namespace App\Components\Helper;

class Scope
{
    const MONTH_RANGE_DEFAULT = 1;

    public $options;

    public $block;
    // 表单
    public $startDate;
    public $endDate;

    public $startTimestamp;
    public $endTimestamp;

    public $displayDates = true;

    public $displayHistoryReq = true;

    public function __construct($params = [], $user = null)
    {
        $this->init($params);
    }

    public function init($params)
    {
        foreach ($params as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }
        $this->endDate = isset($params['endDate']) ? date('Y-m-t', is_numeric($params['endDate']) ? $params['endDate'] / 1000 : strtotime($params['endDate'])) : date('Y-m-t',strtotime('-1month'));
        $this->startDate = isset($params['startDate'])
            ? date('Y-m-01', is_numeric($params['startDate']) ? $params['startDate'] / 1000 : strtotime($params['startDate']))
            : date('Y-m-01', mktime(0,0,0,date('m') - self::MONTH_RANGE_DEFAULT, date('d'), date('Y')));

    }

    public function enableDates()
    {
        $this->displayDates = true;
    }

    public function disableDates()
    {
        $this->displayDates = false;
    }

    public function enableHistoryReq()
    {
        $this->displayHistoryReq = true;
    }

    public function disableHistoryReq()
    {
        $this->displayHistoryReq = false;
    }

}