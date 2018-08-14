<?php

namespace App\Console\Commands;

use App\Console\Components\BaseCommand;
use App\Http\Components\Helpers\QywxHelper;
use App\Models\Crm\Principal;
use App\Models\Crm\Product;
use App\Models\Crm\Proportion;
use App\Models\Crm\Reconciliation;
use App\User;

class ProportionCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:proportion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '比例填充';

    public function handle()
    {
        $end = date('Y-m-t 23:59:59', strtotime('-1month'));
        $start = date('Y-m-01 00:00:00', strtotime('-1month'));
        $billingCycle = date('Y-m-t 23:59:59', strtotime('-2month'));
        $p = Product::getList();
        foreach ($p as $k => $v) {
            $ret = Reconciliation::where(['product_id' => $k])->whereBetween('billing_cycle_start', [$start, $end])->get()->toArray();
            $ops = Principal::where(['product_id' => $k, 'job_id' => Principal::OPS])->first();
            $user = User::findOrFail($ops->principal_id);
            foreach ($ret as $v) {
                $proprotion = Proportion::where(['product_id' => $k, 'billing_cycle' => $billingCycle, 'client' => $v['client'], 'backstage_channel' => $v['backstage_channel']])->first();
                if ($proprotion) {
                    Proportion::create(['product_id' => $k, 'billing_cycle' => $v['billing_cycle_end'], 'client' => $v['client'], 'backstage_channel' => $v['backstage_channel'],
                        'channel_rate' => $proprotion['channel_rate'], 'first_division' => $proprotion['first_division'], 'first_division_remark' => $proprotion['first_division_remark'], 'second_division' => $proprotion['second_division'],
                        'second_division_remark' => $proprotion['second_division_remark'], 'second_division_condition' => $proprotion['second_division_condition'], 'review_type' => 1]);
                } else {
                    Proportion::create(['product_id' => $k, 'billing_cycle' => $v['billing_cycle_end'], 'client' => $v['client'], 'backstage_channel' => $v['backstage_channel'], 'review_type' => 1]);
                }
            }
            QywxHelper::push($user->username,sprintf('你好！%s,%s%s月的流水审计转存完成，请及时处理:%s',$user->username, $v,
                date('m', strtotime($start)),route('reconciliationAudit',
                    ['source' => Reconciliation::OPERATION,'product_id' => $k])),time());
        }
    }
}
