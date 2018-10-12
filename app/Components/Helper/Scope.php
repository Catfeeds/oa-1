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

    public $defaultDateRange = 7;

    public $displayLastMonth = true;

    public $showDate = true;

    public $displayDates = true;

    public $displayHistoryReq = true;

    public function __construct($params = [], $user = null)
    {
        foreach ($params as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }

        if($this->displayLastMonth) {
            $this->endDate = isset($params['endDate']) ? date('Y-m-t', is_numeric($params['endDate']) ? $params['endDate'] / 1000 : strtotime($params['endDate'])) : date('Y-m-t',strtotime('-1month'));
            $this->startDate = isset($params['startDate'])
                ? date('Y-m-01', is_numeric($params['startDate']) ? $params['startDate'] / 1000 : strtotime($params['startDate']))
                : date('Y-m-01', mktime(0,0,0,date('m') - self::MONTH_RANGE_DEFAULT, date('d'), date('Y')));
        } else {

            $this->endDate = isset($params['endDate']) ? $params['endDate'] : date('Y-m-t', time());
            $this->startDate = isset($params['startDate']) ? $params['startDate'] : date('Y-m-01', time());
        }

        if($this->showDate) {
            $this->endDate = isset($params['endDate']) ? $params['endDate'] : DataHelper::asDate();
            $this->startDate = isset($params['startDate']) ? $params['startDate'] : date('Y-m-d', strtotime($this->endDate) - $this->defaultDateRange * 86400);
        }
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