<?php
/**
 * User: xiaoqing Email: liuxiaoqing437@gmail.com
 * Date: 2017/3/27
 * Time: 上午11:23
 * 通用搜索基类
 */

namespace App\Components\Helper;


class GeneralScope extends Scope
{
    public $block; // 搜索扩展区块
    public $set_after_block; // 置后搜索扩展区块
    public $where;

    public $startTimestamp;
    public $endTimestamp;

    public function __construct(array $params, $user)
    {
        parent::__construct($params, $user);

        $this->startTimestamp = strlen($this->startDate) == 19 ? $this->startDate : $this->startDate . ' 00:00:00';
        $this->endTimestamp = strlen($this->endDate) == 19 ? $this->endDate : $this->endDate . ' 23:59:59';

        $this->setWhere();
    }

    public function setWhere($tableAlias = null)
    {
        if ($tableAlias !== null) {
            $tableAlias = sprintf('%s.', $tableAlias);
        }

        return $this->where;
    }

    public function getWhere()
    {
        return is_null($this->where) ? [] : $this->where;
    }

    /**
     * 获取时间相关查询条件
     * @param null $tableAlias
     * @param string $field
     * @return string
     */
    protected function getDateWhere($tableAlias = null, $field = 'created_at')
    {
        if (!$this->displayDates) return ' 1 = 1';

        return sprintf("%s%s BETWEEN '%s' AND '%s'", $tableAlias, $field, $this->startTimestamp, $this->endTimestamp);
    }

    protected function commonCondition($tableAlias, $field, $value)
    {
        return sprintf("%s%s IN(%s)", $tableAlias, $field, $value);
    }
}