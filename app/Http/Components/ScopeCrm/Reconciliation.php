<?php
/**
 * User: xiaoqing Email: liuxiaoqing437@gmail.com
 * Date: 2017/3/27
 * Time: 下午3:37
 * 玩家反馈查询条件
 */

namespace App\Http\Components\ScopeCrm;

use App\Components\Helper\GeneralScope;

class Reconciliation extends GeneralScope
{
    public $os;
    public $backstage_channel;
    public $unified_channel;
    public $client;
    public $review_type;
    public function __construct(array $params, $user = null)
    {
        $this->os = $params['os'] ?? '';
        $this->backstage_channel = $params['backstage_channel'] ?? '';
        $this->unified_channel = $params['unified_channel'] ?? '';
        $this->client = $params['client'] ?? '';
        $this->review_type = $params['review_type'] ?? '';
        parent::__construct($params, $user);
    }

    public function setWhere($tableAlias = 'a.', $excludes = [])
    {
        parent::setWhere($tableAlias); // TODO: Change the autogenerated stub
        if(!empty($this->os)) {
            $where[] = sprintf("%sos = '%s'", $tableAlias, $this->os);
        }

        if(!empty($this->backstage_channel)) {
            $where[] = sprintf("%sbackstage_channel like '%s%s%s'", $tableAlias,'%', $this->backstage_channel, '%');
        }

        if(!empty($this->client)) {
            $where[] = sprintf("%sclient like '%s%s%s'", $tableAlias,'%', $this->client, '%');
        }

        if(!empty($this->review_type)) {
            $where[] = sprintf("%sreview_type = %d", $tableAlias, $this->review_type);
        }

        if(!empty($this->unified_channel)) {
            $where[] = sprintf("%sunified_channel like '%s%s%s'", $tableAlias,'%' , $this->unified_channel, '%');
        }

        $this->where = empty($where) ? '1 = 1' : implode(' AND ', $where);
    }
}