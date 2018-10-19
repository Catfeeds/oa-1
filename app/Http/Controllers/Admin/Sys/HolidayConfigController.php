<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/7
 * Time: 16:49
 * 假期管理配置控制
 */

namespace App\Http\Controllers\Admin\Sys;

use App\Http\Controllers\Controller;
use App\Models\Sys\HolidayConfig;
use App\Models\UserExt;
use App\Models\UserHoliday;
use App\User;
use Illuminate\Http\Request;

class HolidayConfigController extends Controller
{
    protected $redirectTo = '/admin/sys/holiday-config';

    private $_validateRule = [
        'holiday' => 'required|unique:users_holiday_config,holiday|max:20',
        'memo' => 'required',
        'num' => 'numeric'
    ];

    public function index()
    {
        $data = HolidayConfig::orderBy('sort', 'desc')->paginate();
        $title = trans('app.申请类型配置列表');
        return view('admin.sys.holiday-config', compact('title', 'data'));
    }

    public function create()
    {
        $holiday = (object)[
            'exceed_change_id' => '',
            'cypher_type' => '',
            'work_relief_formula' => '{0,0,0,0,0,0}',
            'payable_reset_formula' => '{0,0,0,0,0,0}',
            'payable_claim_formula' => '{0,0,0,0,0,0}',

        ];
        $title = trans('app.添加申请类型配置');
        return view('admin.sys.holiday-config-edit', compact('title', 'holiday'));
    }

    public function edit($id)
    {
        $holiday = HolidayConfig::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('app.申请类型配置')]);
        return view('admin.sys.holiday-config-edit', compact('title', 'holiday'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);
        $p = $request->all();

        $p['num'] = 2;
        $p['is_boon'] = 0;
        $p['change_type'] = 0;


        HolidayConfig::create($p);

       //self::setUserHoliday($p, $holidayConfig->holiday_id);

        flash(trans('app.添加成功', ['value' => trans('app.申请类型配置')]), 'success');

        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {

        $holidayConfig = HolidayConfig::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'holiday' => 'required|max:50|unique:users_holiday_config,holiday,' . $holidayConfig->holiday.',holiday',
        ]));
        $p = $request->all();
        $holidayConfig->update($p);

        //self::setUserHoliday($p, $holidayConfig->holiday_id);

        flash(trans('app.编辑成功', ['value' => trans('app.申请配置')]), 'success');
        return redirect($this->redirectTo);
    }

    public function setUserHoliday($params, $holidayId)
    {
        //如果是福利假,每个员工加入假期
        if($params['is_boon'] == 1) {
            $where = [];
            switch ($params['restrict_sex']) {
                case 0:
                    $where[] = 'sex = 0';
                    break;
                case 1:
                    $where[] = 'sex = 1';
                    break;
            }

            $where = empty($where) ? '1 = 1' : implode(' AND ', $where);
            $userExt= UserExt::whereRaw($where)->get();

            foreach ($userExt as $k => $v) {
                if(empty($v->user_id)) continue;
                $data = [
                    'user_id' => $v->user_id,
                    'holiday_id' => $holidayId
                ];
                $userHoliday = UserHoliday::where($data)->first();
                if(!empty($userHoliday->holiday_id)) continue;
                $data['num'] = $params['num'];
                UserHoliday::create($data);
            }
        } else {
            UserHoliday::where(['holiday_id' => $holidayId])->delete();
        }
    }

}