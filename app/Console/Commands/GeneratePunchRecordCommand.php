<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/27
 * Time: 10:21
 */

namespace App\Console\Commands;


use App\Components\Helper\DataHelper;
use App\Console\Components\BaseCommand;
use App\Models\Attendance\DailyDetail;
use App\User;
use Illuminate\Support\Facades\DB;

class GeneratePunchRecordCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     * @username 员工工号(可选 例如 sy0001）
     * @time 生成时间(可选 例如 2018-11-27)
     * @var string
     */
    protected $signature = 'punch:run {username?} {time?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '预创建每日打卡信息';

    public function handle()
    {
        $username = $this->argument('username');
        $time = $this->argument('time');

        $users = $this->userList($username, $time);

        if(empty($time)) $time = date('Y-m-d', time());

        return $this->generateDailyPunch($users, $time);
    }

    public function generateDailyPunch($users, $time)
    {
        if(empty($users)) return;

        DB::beginTransaction();
        try {
            foreach ($users as $k => $v) {
                $data = [
                    'user_id' => $v['user_id'],
                    'day' => DataHelper::dateTimeFormat($time, 'Y-m-d'),
                    'status' => DailyDetail::GENERATE_WAIT_IMPORT,
                ];
                $check = DailyDetail::where(['user_id' => $v['user_id'], 'day' => $time])->first();

                if(!empty($check->user_id)) {

                    \Log::info($v['alias'] .'('. $v['username'] .')已生成'.$time.'考勤信息');
                    continue;
                }
                DailyDetail::create($data);
                \Log::info($v['alias'] . '('. $v['username'] .')生成'.$time.'考勤信息完成');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            \Log::info($v['alias'] . '('. $v['username'] .')生成'.$time.'考勤信息失败');
        }
        DB::commit();
    }

    public function userList($username, $time)
    {
        if ((!empty($username) && !empty($time)) || (!empty($username) && empty($time))) {
            $user = User::where(['username' => $username])->get()->toArray();
            if(empty($user)) return $this->error('未查询到该工号信息, 请输入正确格式 punch:run 员工工号(可选，例如 sy0001） 生成时间(可选,例如 2018-11-27)');
            return $user;

        } elseif (empty($username) && empty($time)) {

            return User::where(['status' => User::STATUS_ENABLE])->get()->toArray();
        } else {

            return $this->error('请输入正确格式 punch:run 员工工号(可选，例如 sy0001） 生成时间(可选,例如 2018-11-27)');
        }
    }

}