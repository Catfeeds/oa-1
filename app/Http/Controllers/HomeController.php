<?php

namespace App\Http\Controllers;

use App\Http\Components\Helpers\PunchHelper;
use App\Http\Controllers\Material\MaterialController;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Material\Apply;
use App\Models\Sys\Bulletin;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\PunchRulesConfig;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bullets = Bulletin::where([
            [\DB::raw('UNIX_TIMESTAMP(end_date)'), '>=', time()],
            [\DB::raw('UNIX_TIMESTAMP(start_date)'), '<=', time()],
            ['show', '=', 1],
        ])
            ->orderBy('weight', 'desc')->orderBy('created_at', 'desc')->get();
        $start = date('Y-m-01', strtotime('-1 month'));
        $remainWelfare = $this->remain();
        $countRecheck = $this->countRecheck($start);
        $apply = $this->apply($start, [Leave::ON_REVIEW, Leave::PASS_REVIEW]);
        $approve = $this->approve($start, [Leave::ON_REVIEW, Leave::PASS_REVIEW,Leave::WAIT_REVIEW]);
        //dd($formulaCalPunRuleConf = PunchHelper::getCalendarPunchRules('2018-10-01', '2018-10-31')['formula']);
        /*$formulaCalPunRuleConf = PunchHelper::getCalendarPunchRules('2018-10-01', '2018-10-31')['formula']['2018-10-11'];
        $a = [
            ['start' => '19:45', 'end' => '20:00'],
            ['start' => '10:00', 'end' => '10:45'],
            ['start' => '10:00', 'end' => '12:00'],
            ['start' => '10:25', 'end' => '14:45'],
            ['start' => '19:00', 'end' => '20:10'],
        ];
        dump(PunchHelper::combine($a));
        dd(PunchHelper::getFormulaCombine(PunchHelper::combine($a), $formulaCalPunRuleConf));*/
        /*dd(PunchHelper::getFormulaDelayConf(['go' => ['start' => '9:15', 'end' => '9:40'], 'off' => ['start' => '19:25', 'end' => '20:00']], $formulaCalPunRuleConf));*/
        /*dd(PunchHelper::getFormulaNightConf(['start' => '9:15', 'end' => '15:15'], $formulaCalPunRuleConf));*/
//        dd(PunchRulesConfig::resolveGapFormula('[0,0,1,0,0]'));
        return view('home', compact('bullets', 'remainWelfare', 'countRecheck', 'apply', 'approve'));
    }

    public function remain()
    {
        $holidayConfig = HolidayConfig::whereIn('show_name', ['年假', '调休假', '探亲假'])->get();
        $remainWelfare = [];
        foreach ($holidayConfig as $k => $v) {
            $driver = HolidayConfig::$cypherTypeChar[$v->cypher_type];
            $remainWelfare[] = \AttendanceService::driver($driver, 'cypher')->getUserHoliday(\Auth::user()->userExt->entry_time, \Auth::user()->user_id, $v);
        }
        return $remainWelfare;
    }

    public function countRecheck($start)
    {
        return [
            'start' => DailyDetail::whereBetween('day', [$start, date('Y-m-d')])
                ->where('user_id', \Auth::user()->user_id)->whereNull('punch_start_time')->count(),
            'end'   => DailyDetail::whereBetween('day', [$start, date('Y-m-d')])
                ->where('user_id', \Auth::user()->user_id)->whereNull('punch_end_time')->count(),
        ];
    }

    public function apply($start, $status)
    {
        $arr = Leave::where(['user_id' => \Auth::user()->user_id])
            ->whereIn('status', $status)
            ->whereBetween('start_time', [$start, date('Y-m-d')])->groupBy('status')
            ->get([\DB::raw('count(leave_id) as c'), 'status'])->pluck('c', 'status')->toArray();

        $review = Leave::where(['review_user_id' => \Auth::user()->user_id, 'status' => 0])
            ->whereBetween('start_time', [$start, date('Y-m-d')])->count();
        return ['apply' => $arr, 'review' => $review];
    }

    public function approve($start, $status)
    {
        $leaveApprove = $materialApprove = [];
        if (\Entrust::can('leave.review')) {
            $leaveApprove = Leave::whereBetween('start_time', [$start, date('Y-m-d')])
                ->whereIn('status', $status)
                ->where('review_user_id', \Auth::user()->user_id)
                ->orderBy('created_at', 'desc')->limit(5)->get()->toArray();
        }
        if (\Entrust::can('material.approve*')) {
            $materialApprove = Apply::whereBetween('created_at', [$start, date('Y-m-d')])->with('inventory')
                ->where('review_user_id', \Auth::user()->user_id)->orderBy('created_at', 'desc')->limit(5)->get()->toArray();
            $materialApprove = app(MaterialController::class)->handleData($materialApprove, ['type', 'name']);
        }
        return ['leave' => $leaveApprove, 'material' => $materialApprove];
    }
}
