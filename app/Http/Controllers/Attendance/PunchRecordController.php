<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/21
 * Time: 16:09
 */

namespace App\Http\Controllers\Attendance;

use App\Components\Helper\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\PunchRecord;
use App\Models\Sys\Calendar;
use App\User;
use Illuminate\Http\Request;


class PunchRecordController extends Controller
{
    private $_validateRule = [
        'memo' => 'unique:punch_record,memo|max:32',
    ];

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
            'memo' => $request->get('memo'),
            'name' => $request->file($file)->getClientOriginalName() ?? '',
            'annex' => $filePath ?? '',
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
            'memo' => 'required|max:255|unique:punch_record,memo,' . $punchRecord->id,
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
        try {
            //reader读取excel内容
            \Excel::load(storage_path($punchRecord->annex), function ($reader) use ($punchRecord) {

                $reader = $reader->getSheet(0);
                $reader = $reader->toarray();
                $data = $msgArr = [];
                foreach ($reader as $k => $v) {
                    if ($v[0] == null || $k == 0) continue;
                    //去除空值
                    $v = array_filter($v);
                    //转换成1970年以来的秒数,用来显示日期
                    //$n = intval(($v[0] - 25569) * 3600 * 24);

                    $endTime = end($v);
                    //打卡记录里面的第五列是打卡开始时间
                    $startTime = $v[5];
                    if (count($v) <= 6 && (int)str_replace(':', '', $v[5]) >= 1400) {
                        $startTime = NULL;
                    } elseif (count($v) <= 6 && (int)str_replace(':', '', $v[5]) <= 1400) {
                        $endTime = NULL;
                    }
                    $ts = str_replace('/', '-', $v[0]);
                    $row = [
                        'ts'         => $ts,
                        'alias'      => $v[3],
                        'start_time' => $startTime,
                        'end_time'   => $endTime,
                    ];

                    $data[$ts][] = $row;
                }

                foreach ($data as $dk => $dv) {
                    list($year, $month, $day) = explode('-', $dk);
                    $calendar = Calendar::with('punchRules')
                        ->where(['year' => $year, 'month' => $month, 'day' => $day])
                        ->first();
                    if (empty($calendar->punch_rules_id)) {
                        $msgArr[] = '未匹配到[' . $year . '-' . $month . '-' . $day . ']日期考勤规则设置,导入失败!';
                        continue;
                    }

                    $punchRuleStartTime = strtotime($dk . ' ' . $calendar->punchRules->work_start_time);
                    $punchRuleEndTime = strtotime($dk . ' ' . $calendar->punchRules->work_end_time);

                    foreach ($dv as $u) {
                        $user = User::where(['alias' => $u['alias']])->first();
                        if (empty($user->alias)) {
                            $msgArr[] = '未找到[' . $u['alias'] . ']员工信息!';
                            continue;
                        }

                        //迟到分数计算
                        $startTimeNum = empty($u['start_time']) ? 0 : strtotime($dk . ' ' . $u['start_time']);
                        $endTimeNum = empty($u['end_time']) ? 0 : strtotime($dk . ' ' . $u['end_time']);
                        $startNum = !empty($startTimeNum) && $startTimeNum > $punchRuleStartTime ? ($startTimeNum - $punchRuleStartTime) / 60 : 0;
                        $endNum = !empty($endTimeNum) && $endTimeNum < $punchRuleEndTime ? ($punchRuleEndTime - $endTimeNum) / 60 : 0;

                        $dailyDetail = [
                            'user_id'              => $user->user_id,
                            'day'                  => $dk,
                            'punch_start_time'     => $u['start_time'],
                            'punch_start_time_num' => $startTimeNum,
                            'punch_end_time'       => $u['end_time'],
                            'punch_end_time_num'   => $endTimeNum,
                            'heap_late_num'        => $startNum + $endNum,
                            'lave_buffer_num'      => 0,
                            'deduction_num'        => $startNum + $endNum,
                        ];

                        $userDailyDetail = DailyDetail::where(['user_id' => $user->user_id, 'day' => $dk])->first();
                        if (!empty($userDailyDetail->user_id)) {

                            //更新表中的迟到分数
                            $hn = $dn = 0;
                            if (empty($userDailyDetail->punch_start_time) && !empty($userDailyDetail->punch_end_time)) {
                                $hn = $dn = $startNum;
                            }
                            if (!empty($userDailyDetail->punch_start_time) && empty($userDailyDetail->punch_end_time)) {
                                $hn = $dn = $endNum;
                            }

                            //向审核通过时插入的数据中添加excel打卡表中正常打卡的数据
                            $userDailyDetail->update([
                                'punch_start_time'     => $userDailyDetail->punch_start_time ?? $u['start_time'],
                                'punch_start_time_num' => $userDailyDetail->punch_start_time_num ?: $startTimeNum,
                                'punch_end_time'       => $userDailyDetail->punch_end_time ?? $u['end_time'],
                                'punch_end_time_num'   => $userDailyDetail->punch_end_time_num ?: $endTimeNum,
                                'heap_late_num'        => $userDailyDetail->heap_late_num ?: $hn,
                                'lave_buffer_num'      => 0,
                                'deduction_num'        => $userDailyDetail->deduction_num ?: $dn,
                            ]);
                            $msgArr[] = $u['alias'] . '员工已导入[' . $dk . ']考勤记录!';
                            continue;
                        }

                        DailyDetail::create($dailyDetail);

                        $msgArr[] = $u['alias'] . '员工导入[' . $dk . ']考勤记录成功!';
                    }
                }
                //信息记录
                $strArr = '<?php return ' . var_export($msgArr, true) . ';';
                $logFile = storage_path('app/punch-record/' . date('Ymd', time()) . '/' . $punchRecord->id . '_punch_log.txt');
                file_put_contents($logFile, $strArr, LOCK_EX);
                $punchRecord->update(['log_file' => $logFile, 'status' => 3]);

            });
        } catch (\Exception $e) {
            $punchRecord->update(['status' => 2]);
            flash('生成失败，打卡文件有误，无法解析!', 'danger');
            return redirect()->route('daily-detail.review.import.info');
        }

        flash(trans('att.生成成功员工每日打卡明细'), 'success');
        return redirect()->route('daily-detail.review.import.info');
    }

    public function log($id)
    {
        $punchRecord = PunchRecord::findOrFail($id);

        if (empty($punchRecord->status) || $punchRecord->status != 3 || empty($punchRecord->log_file)) {

            return redirect()->route('daily-detail.review.import.info');
        }

        $data = require $punchRecord->log_file;

        $title = trans('att.生成日志查看');
        return view('attendance.punch-record.log', compact('title', 'data'));
    }
}