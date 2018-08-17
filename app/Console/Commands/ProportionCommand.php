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
        $cycle = date('Y-m', strtotime('-1month'));
        $billingCycle = date('Y-m-t 23:59:59', strtotime('-2month'));
        $p = Product::getList();
        foreach ($p as $k => $val) {
            $ret = Reconciliation::where(['product_id' => $k, 'billing_cycle' => $cycle])->get()->toArray();
            $isHasProduct = Principal::where(['product_id' => $k])->get()->toArray();
            if (!$isHasProduct) {
                foreach (Principal::JOB as $job => $jobName) {
                    Principal::create(['product_id' => $k, 'job_id' => $job]);
                }
            }

            $ops = Principal::where(['product_id' => $k, 'job_id' => Principal::OPS])->first();

            foreach ($ret as $v) {
                $proprotion = Proportion::where(['product_id' => $k, 'billing_cycle' => $billingCycle, 'client' => $v['client'], 'backstage_channel' => $v['backstage_channel']])->first();
                $has = Proportion::where(['product_id' => $k, 'rec_id' =>  $v['id']])->first();
                if ($has) {
                    continue;
                }
                if ($proprotion) {
                    Proportion::create(['product_id' => $k, 'billing_cycle' => $v['billing_cycle_end'], 'client' => $v['client'], 'backstage_channel' => $v['backstage_channel'],
                        'channel_rate' => $proprotion['channel_rate'], 'first_division' => $proprotion['first_division'], 'first_division_remark' => $proprotion['first_division_remark'], 'second_division' => $proprotion['second_division'],
                        'second_division_remark' => $proprotion['second_division_remark'], 'second_division_condition' => $proprotion['second_division_condition'], 'rec_id' => $v['id'], 'review_type' => 1]);
                } else {
                    Proportion::create(['product_id' => $k, 'billing_cycle' => $v['billing_cycle_end'], 'client' => $v['client'], 'backstage_channel' => $v['backstage_channel'], 'rec_id' => $v['id'],'review_type' => 1]);
                }
            }
            if (isset($ops['principal_id'])) {
                try {
                    $user = User::findOrFail($ops['principal_id']);
                    QywxHelper::push($user->username, sprintf('你好！%s,%s%s月的流水审计拉取完成，请及时处理:%s', $user->username, $val, date('m', strtotime($cycle)), route('reconciliationAudit', ['source' => Reconciliation::OPERATION, 'product_id' => $k])), time());
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }
            }
        }
    }
}
