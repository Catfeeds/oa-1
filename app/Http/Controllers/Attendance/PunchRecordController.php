<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/21
 * Time: 16:09
 */

namespace App\Http\Controllers\Attendance;

use App\Components\Helper\DataHelper;
use App\Components\Helper\FileHelper;
use App\Http\Components\Helpers\PunchHelper;
use App\Http\Controllers\Controller;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Attendance\PunchRecord;
use App\Models\Sys\Calendar;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\PunchRules;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class PunchRecordController extends Controller
{
    private $_validateRule = [
        'memo' => 'max:32',
    ];

    public $punchHelper;

    public function __construct(PunchHelper $punchHelper)
    {
        $this->punchHelper = $punchHelper;
    }

    public function index()
    {
        $data = PunchRecord::paginate(30);

        $title = trans('att.打卡导入记录');
        return view('attendance.punch-record.index', compact('title', 'data', 'scope'));
    }

    public function create()
    {
        $title = trans('att.打卡记录导入');
        return view('attendance.punch-record.edit', compact('title'));
    }

    public function edit($id)
    {
        $punchRecord = PunchRecord::findOrFail($id);
        $title = trans('att.编辑打卡记录导入');
        return view('attendance.punch-record.edit', compact('title', 'punchRecord'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);
        $file = 'annex';
        $filePath = self::setPunchRecordFile($request, $file);

        $data = [
            'memo'   => $request->get('memo'),
            'name'   => $request->file($file)->getClientOriginalName() ?? '',
            'annex'  => $filePath ?? '',
            'status' => 0,
        ];

        PunchRecord::create($data);
        flash(trans('att.上传成功', ['value' => trans('att.打卡记录')]), 'success');
        return redirect()->route('daily-detail.review.import.info');
    }

    public function update(Request $request, $id)
    {
        $punchRecord = PunchRecord::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'memo' => 'required|max:255|unique:attendance_punch_record,memo,' . $punchRecord->id,
        ]));

        $p = $request->all();
        $file = 'annex';
        $filePath = self::setPunchRecordFile($request, $file);

        $data = [
            'memo' => $p['memo'],
        ];
        if (!empty($filePath)) $data['annex'] = $filePath;
        $punchRecord->update($data);

        flash(trans('app.编辑成功', ['value' => trans('app.打卡记录')]), 'success');
        return redirect()->route('daily-detail.review.import.info');
    }


    public function setPunchRecordFile($request, $file)
    {
        $filePath = $fileName = '';

        if ($request->hasFile($file) && $request->file($file)->isValid()) {
            $time = date('Ymd', time());
            $uploadPath = 'app/punch-record/' . $time;
            $fileName = $file . '_' . time() . rand(100000, 999999);
            $fileName = FileHelper::uploadExcel($request->file($file), $fileName, $uploadPath);
            $filePath = $uploadPath . '/' . $fileName;
        }

        return $filePath;
    }

    public function generate($id)
    {
        $punchRecord = PunchRecord::findOrFail($id);

        if (empty($punchRecord->annex)) {
            flash('生成失败，未找到打卡记录文件!', 'danger');
            return redirect()->route('daily-detail.review.info');
        }
        if (empty($nightConf = HolidayConfig::where('cypher_type', HolidayConfig::CYPHER_NIGHT)->first())) {
            flash('未在假期配置中配置夜班加班调休假', 'danger');
            return redirect()->route('daily-detail.review.info');
        }
        $isOK = true;
        //事务开启
        /*DB::beginTransaction();
        try {*/
        //reader读取excel内容
        \Excel::load(storage_path($punchRecord->annex), function ($reader) use ($punchRecord, $isOK, $nightConf) {
            $reader = $reader->getSheet(0);
            $reader = $reader->toarray();
            array_shift($reader);
            $r = collect($reader)->pluck('0')->unique()->map(function ($v) {
                list($m, $d, $y) = explode('-', $v);
                return strtotime(sprintf("20%s-%s-%s", $y, (int)$m, (int)$d));
            });
            $maxTs = $r->max(); $minTs = $r->min(); unset($r);
            $YmdMaxTs = date('Y-m-d', $maxTs);
            $YmdMinTs = date('Y-m-d', $minTs);
            $calendarArr = Calendar::getCalendarArrWithPunchRules($YmdMinTs, $YmdMaxTs);
            $data = $msgArr = $bufferArr = [];
            $users = User::getUserKeyByAlias();
            $nights = Leave::where(['status' => Leave::WAIT_EFFECTIVE, 'holiday_id' => $nightConf->holiday_id])
                ->whereBetween(DB::raw('DATE_FORMAT(start_time, \'%Y-%m-%d\')'), [$YmdMinTs, $YmdMaxTs])->get(['end_time', 'user_id', 'start_time']);
            $formulaCalPunRuleConfArr = PunchHelper::getCalendarPunchRules($YmdMinTs, $YmdMaxTs, false, $calendarArr)['formula'];
            $this->punchHelper->setFormulaCalPunRuleConfArr('', '', $formulaCalPunRuleConfArr);

            foreach ($reader as $k => $v) {
                //if ($v[0] == null || $k == 0) continue;
                //去除空值
                $v = array_filter($v);
                //转换成1970年以来的秒数,用来显示日期
                //$n = intval(($v[0] - 25569) * 3600 * 24);

                //$endTime = end($v);
                //打卡记录里面的第五列是打卡开始时间
//                $startTime = $v[5];
                /*if (count($v) <= 6 && (int)str_replace(':', '', $v[5]) >= 1400) {
                    $startTime = NULL;
                } elseif (count($v) <= 6 && (int)str_replace(':', '', $v[5]) <= 1400) {
                    $endTime = NULL;
                }*/

                $ts = str_replace('/', '-', $v[0]);
                //本地环境格式
                //list($year, $month, $day) = explode('-', $ts);

                //线上环境格式
                list($month, $day, $year) = explode('-', $ts);
                $year = '20' . $year;
                $day = (int)$day;
                $month = (int)$month;
                //线上环境end

                //线上环境
                $ts = sprintf('%d-%02d-%02d', $year, $month, $day);

                $calendar = $calendarArr[$ts] ?? NULL;
                if (empty($calendar->punch_rules_id)) {
                    $isOK = false;
                    $msgArr[] = '未匹配到[' . $year . '-' . $month . '-' . $day . ']日期考勤规则设置,导入失败!';
                    break;
                }

                list($lastDayEndTime, $startTime, $endTime) = $this->punchHelper->dealLastDayEnd($ts, $v, $users, $nights);

                //线上环境end
                $row = [
                    'ts'         => $ts,
                    'alias'      => $v[3],
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                ];

                //修改指定用户上一天的下班时间
                if (isset($lastDayEndTime)) {
                    $data[date('Y-m-d', strtotime("-1 day $ts"))][$v[3]]['end_time'] = $lastDayEndTime;
                    unset($lastDayEndTime);
                }
                $data[$ts][$v[3]] = $row;
                ksort($data);
            }
            unset($calendarArr, $k, $v, $reader);

            if (!$isOK) {
                //信息记录
                $strArr = '<?php return ' . var_export($msgArr, true) . ';';
                $logFile = storage_path('app/punch-record/' . date('Ymd', time()) . '/' . $punchRecord->id . '_punch_log.txt');
                file_put_contents($logFile, $strArr, LOCK_EX);
                throw new \Exception('信息错误');
            }

            $details = DailyDetail::whereBetween('day', [$YmdMinTs, $YmdMaxTs])->get();
            foreach ($data as $dk => $dv) {

                $detailKeyByUser = $details->where('day', $dk)->keyBy('user_id');

                foreach ($dv as $u) {

                    if (empty($u['ts']) || empty($u['alias'])) continue;

                    $user = $users[$u['alias']] ?? NULL;
                    if (empty($user->alias)) {
                        $msgArr[] = '未找到[' . $u['alias'] . ']员工信息!';
                        continue;
                    }

                    $detail = $detailKeyByUser[$user->user_id] ?? NULL;
                    if (count($formulaCalPunRuleConfArr[$u['ts']]) === 0) {
                        $msgArr[] = '[' . $u['ts'] . ']这天未有上下班打卡配置,请先配置';
                        continue;
                    }

                    $deducts = $this->punchHelper->fun_($bufferArr, $u, $detail);
                    $bufferArr = $deducts['bufferArr'];
                    $switchLeaveIds = $this->punchHelper->storeDeductInLeave($deducts['deducts'], $user->user_id, $u['ts']);

                    //迟到分数计算
                    $startTimeNum = empty($u['start_time']) ? 0 : strtotime($dk . ' ' . $u['start_time']);
                    $endTimeNum = empty($u['end_time']) ? 0 : strtotime($dk . ' ' . $u['end_time']);
                    /*$startNum = !empty($startTimeNum) && $startTimeNum > $punchRuleStartTime ? ($startTimeNum - $punchRuleStartTime) / 60 : 0;
                    $endNum = !empty($endTimeNum) && $endTimeNum < $punchRuleEndTime ? ($punchRuleEndTime - $endTimeNum) / 60 : 0;*/

                    $dailyDetail = [
                        'user_id'              => $user->user_id,
                        'day'                  => $dk,
                        'punch_start_time'     => $u['start_time'],
                        'punch_start_time_num' => $startTimeNum,
                        'punch_end_time'       => $u['end_time'],
                        'punch_end_time_num'   => $endTimeNum,
                        'heap_late_num'        => $deducts['deducts']['deduct_score']['minute'] ?? 0,
                        'lave_buffer_num'      => $deducts['deducts']['remain_buffer'] ?? 0,
                        'deduction_num'        => $deducts['deducts']['deduct_score']['score'] ?? 0,
                        'leave_id'             => empty($switchLeaveIds) ? NULL : json_encode($switchLeaveIds),
                        'created_at'           => date('Y-m-d H:i:s', time()),
                        'updated_at'           => date('Y-m-d H:i:s', time()),
                    ];

                    $userDailyDetail = $detailKeyByUser[$user->user_id] ?? NULL;
                    if (!empty($userDailyDetail->user_id)) {

                        //向审核通过时插入的数据中添加excel打卡表中正常打卡的数据
                        $userDailyDetail->update([
                            'punch_start_time'     => $u['start_time'],
                            'punch_start_time_num' => $startTimeNum,
                            'punch_end_time'       => $u['end_time'],
                            'punch_end_time_num'   => $endTimeNum,
                            'heap_late_num'        => $deducts['deducts']['deduct_score']['minute'] ?? 0,
                            'lave_buffer_num'      => $deducts['deducts']['remain_buffer'],
                            'deduction_num'        => $deducts['deducts']['deduct_score']['score'] ?? 0,
                            'leave_id'             => \AttendanceService::driver('operate')->addLeaveId($switchLeaveIds, $userDailyDetail->leave_id),
                        ]);
                        $msgArr[] = $u['alias'] . '员工已导入[' . $dk . ']考勤记录!';
                        continue;
                    }
                    DailyDetail::create($dailyDetail);
                    $msgArr[] = $u['alias'] . '员工导入[' . $dk . ']考勤记录成功!';
                }
            }
            /*DailyDetail::insert($dailyDetail);*/
            unset($bufferArr, $data);
            //信息记录
            $strArr = '<?php return ' . var_export($msgArr, true) . ';';
            $logFile = storage_path('app/punch-record/' . date('Ymd', time()) . '/' . $punchRecord->id . '_punch_log.txt');
            file_put_contents($logFile, $strArr, LOCK_EX);
            $punchRecord->update(['log_file' => $logFile, 'status' => 3]);
        });
        /* } catch (\Exception $e) {
             //事务回滚
             DB::rollBack();
             $punchRecord->update(['status' => 2]);

             flash('生成失败，生成文件有误，无法解析!', 'danger');
             return redirect()->route('daily-detail.review.import.info');
         }
         //事务提交
         DB::commit();
         flash(trans('att.生成成功员工每日打卡明细'), 'success');
         return redirect()->route('daily-detail.review.import.info');*/
    }

    public function log($id)
    {
        $punchRecord = PunchRecord::findOrFail($id);

        if (empty($punchRecord->status) || empty($punchRecord->log_file)) {

            return redirect()->route('daily-detail.review.import.info');
        }

        $data = require $punchRecord->log_file;

        $title = trans('att.生成日志查看');
        return view('attendance.punch-record.log', compact('title', 'data'));
    }
}