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
        'name' => 'required|unique:punch_record,name|max:32',
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
        return view('attendance.leave.edit', compact('title', 'punchRecord'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);
        $p = $request->all();

        $file = 'annex';
        $filePath = $fileName = '';
        if ($request->hasFile($file) && $request->file($file)->isValid()) {
            $time = date('Ymd', time());
            $uploadPath = 'app/punch-record/'. $time;
            $fileName = $file .'_'. time() . rand(100000, 999999);
            $fileName = FileHelper::uploadExcel($request->file($file), $fileName, $uploadPath);
            $filePath = $uploadPath .'/'. $fileName;
        }

        $data = [
            'name' => $p['name'],
            'annex' => $filePath ?? ''
        ];

        PunchRecord::create($data);
        flash(trans('att.上传成功', ['value' => trans('att.打卡记录')]), 'success');
        return redirect()->route('daily-detail.review.import.info');
    }

    public function generate($id)
    {
        $punchRecord = PunchRecord::findOrFail($id);

        if(empty($punchRecord->annex)) {
            flash('生成失败，未找到打卡记录文件!', 'danger');
            return redirect()->route('daily-detail.review.info');
        }
        //try{
        //reader读取excel内容
        \Excel::load(storage_path($punchRecord->annex), function($reader){

            $reader = $reader->getSheet(0);
            $reader = $reader->toarray();

            $data = [];
            foreach ($reader as $k => $v) {
                if ($v[0] == null || $k == 0) continue;
                //去除空值
                $v = array_filter($v);
                //转换成1970年以来的秒数,用来显示日期
                $n = intval(($v[0] - 25569) * 3600 * 24);

                $endTime = end($v);
                //打卡记录里面的第五列是打卡开始时间
                if(key($v) == 5) $endTime = '';
                $ts = gmdate('Y-m-d', $n);
                $row = [
                    'ts' => $ts,
                    'alias' => $v[3],
                    'start_time' => $v[5],
                    'end_time' => $endTime,
                ];

                $data[$ts][] = $row;
            }

            foreach ($data as $dk => $dv) {
                list($year, $month, $day) = explode('-', $dk);
                $calendar = Calendar::with('punchRules')
                    ->where(['year' => $year, 'month' => $month, 'day' => $day])
                    ->first();
                if(empty($calendar->punch_rules_id)) continue;

                $punchRuleStartTime = strtotime($dk . ' ' . $calendar->punchRules->work_start_time);
                $punchRuleEndTime = strtotime($dk . ' ' . $calendar->punchRules->work_end_time);;

                foreach ($dv as $u) {

                    $user = User::where(['alias' => $u['alias']])->first();
                    if(empty($user->alias)) continue;
                    $userDailyDetail = DailyDetail::where(['user_id' => $user->user_id, 'day' => $dk])->first();
                    if(!empty($userDailyDetail->user_id)) continue;

                    $startTimeNum = strtotime($dk . ' ' . $u['start_time']);
                    $endTimeNum = strtotime($dk . ' ' . $u['end_time']);

                    $startNum = $startTimeNum > $punchRuleStartTime ? ($startTimeNum - $punchRuleStartTime)/60  : 0;
                    $endNum = $endTimeNum < $punchRuleEndTime ? ($punchRuleEndTime - $endTimeNum)/60 : 0 ;

                    $dailyDetail = [
                        'user_id' => $user->user_id,
                        'day' => $dk,
                        'punch_start_time' => $u['start_time'],
                        'punch_start_time_num' => $startTimeNum,
                        'punch_end_time' => $u['end_time'],
                        'punch_end_time_num' => $endTimeNum,
                        'heap_late_num' => $startNum + $endNum,
                        'lave_buffer_num' => 0,
                        'deduction_num' => $startNum + $endNum,
                    ];

                    DailyDetail::create($dailyDetail);
                }
           }

          /*  \Log::useFiles(storage_path('app/punch-record/'. date('Ymd', time()). '/punch_log.txt'));
            \Log::info($u['alias']);*/
        });
        /*            } catch (\Exception $e) {
                flash('生成失败，打卡文件有误，无法解析!', 'danger');
                return redirect()->route('daily-detail.review.import.info');
            }*/


        flash(trans('att.生成成功员工每日打卡明细'), 'success');
        return redirect()->route('daily-detail.review.import.info');
    }
}