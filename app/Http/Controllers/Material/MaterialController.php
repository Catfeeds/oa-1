<?php

namespace App\Http\Controllers\Material;

use App\Components\AttendanceService\Operate\Material;
use App\Http\Components\Helpers\AttendanceHelper;
use App\Http\Components\Helpers\OperateLogHelper;
use App\Models\Material\Apply;
use App\Models\Sys\Dept;
use App\Models\Sys\Inventory;
use App\Models\Sys\OperateLog;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MaterialController extends Controller
{
    public function applyIndex()
    {
        $title = '资质外借申请';
        $inventory = Inventory::get();
        $data = Apply::where(['user_id' => \Auth::user()->user_id])->with('inventory')->get()->toArray();
        $applies = $this->handleData($data);
        return view('material.apply', compact('title', 'inventory', 'applies'));
    }

    public function applyCreate(Request $request)
    {
        $title = '资质借用申请确认';
        $ids = $request->inventoryIds;
        $inventory = Inventory::whereIn('id', json_decode($ids, true))->get();
        return view('material.apply-edit', compact('title', 'inventory', 'ids'));
    }

    public function applyStore(Request $request)
    {
        $inventoryIds = json_decode($request->inventory_ids, true);
        $appInvData = [];
        $data = $request->all();
        $data['user_id'] = \Auth::user()->user_id;
        $data['annex'] = AttendanceHelper::setAnnex($request);
        //申请单检验

        //获取申请单审核步骤流程
        $step = app(Material::class)->getLeaveStep();
        if(!$step['success']) return redirect()->back()->with('')->withInput()->withErrors($step['message']);
        $leave = array_merge($data, $step['data']);

        \DB::beginTransaction();
        try {
            $matApply = Apply::create($leave);
            OperateLogHelper::createOperateLog(OperateLogHelper::MATERIAL, $matApply->id, '提交申请');
            foreach ($inventoryIds as $id) {
                $appInvData[] = ['apply_id' => $matApply->id, 'inventory_id' => $id];
            }
            \DB::table('material_apply_inventory')->insert($appInvData);
        }catch (\Exception $exception) {
            \DB::rollBack();
            flash('申请失败', 'danger');
            return redirect()->route('material.apply.index');
        }
        \DB::commit();
        flash('申请成功', 'success');
        return redirect()->route('material.apply.index');
    }

    public function optInfo($id)
    {
        $title = '申请单明细';
        $apply = Apply::with('inventory')->find($id)->toArray();
        $dept = Dept::getDeptList();
        $user = User::with(['dept'])->where(['user_id' => $apply['review_user_id']])->first();
        $logs = OperateLog::where(['type_id' => OperateLogHelper::MATERIAL, 'info_id' => $id])->get();
        return view('material.apply-info', compact('title', 'apply', 'dept', 'user', 'logs'));
    }

    public function approveIndex()
    {
        $title = '资质外借审批';
        $state = 'all';
        $ids = OperateLogHelper::getLogInfoIdToUid(\Auth::user()->user_id, OperateLogHelper::MATERIAL);
        $data = Apply::where(function ($query) use ($ids){
            $query->whereIn('id', $ids)->orWhereRaw('review_user_id = '.\Auth::user()->user_id);
        })
            ->orderBy('created_at', 'desc')->with('inventory')->paginate(30)->toArray();
        $applies = $this->handleData($data['data'], ['name', 'type', 'company']);
        return view('material.approve', compact('title', 'applies', 'state'));
    }

    public function handleData($applies, array $types = ['name'])
    {
        $typeArr = [];
        foreach ($applies as $key => &$apply) {
            foreach ($apply['inventory'] as $inv) {
                foreach ($types as $type) {
                    $typeArr[$type] = $typeArr[$type] ?? '';
                    $typeArr[$type] = $typeArr[$type].'/'.$inv[$type];
                }
            }
            foreach ($types as $type) {
                $apply['inventory_'.$type] = trim($typeArr[$type], '/');
            }
        }
        return $applies;
    }

    public function selectByState($state)
    {
        $title = '资质外借审批';
        $s = $state == 'all' ? '*' : $state;
        $data = Apply::where(['state' => $s, 'review_user_id' => \Auth::user()->user_id])
            ->with('inventory')->get()->toArray();
        $applies = $this->handleData($data, ['name', 'type', 'company']);
        return view('material.approve', compact('title', 'applies', 'state'));
    }

    public function reviewOptStatus(Request $request, $id)
    {
        $status = $request->status;
        if(!in_array($status, array_keys(Apply::$stateChar)) || empty($id))
            return redirect()->back();

        $optStatus = $this->OptStatus($id, $status, $request);
        if($optStatus) {
            return redirect()->back();
        }
    }

    public function OptStatus($id, $status, $request)
    {
        $apply = Apply::with('inventory')->findOrFail($id);

        if($apply->status === $status) return false;

        if(empty($apply->id) || $apply->review_user_id != \Auth::user()->user_id) {
            return false;
        }

        $msg = '';
        //mysql事物开始
        \DB::beginTransaction();
        try {
            switch ($status) {
                //拒绝通过状态
                case Apply::APPLY_FAIL:
                    $msg = '拒绝通过';
                    $apply->update(['state' => Apply::APPLY_FAIL, 'review_user_id' => 0]);
                    break;
                //审核通过状态
                case Apply::APPLY_PASS:
                    $msg = '审核通过';
                    //申请单状态操作
                    $driver = 'material';
                    \AttendanceService::driver($driver)->leaveReviewPass($apply);
                    break;
                case Apply::APPLY_CANCEL:
                    $msg = '取消';
                    $apply->update(['state' => Apply::APPLY_CANCEL, 'review_user_id' => 0]);
                    break;
                case Apply::APPLY_RETURN:
                    dd('hello');
            }
            OperateLogHelper::createOperateLog(OperateLogHelper::MATERIAL, $apply->id, $msg);

        } catch (\Exception $ex) {
            //mysql事物回滚
            \DB::rollBack();
            return false;
        }
        //mysql事物提交
        \DB::commit();
        return true;
    }

    public function redrawApply($id)
    {
        $apply = Apply::findOrFail($id);
        if(in_array($apply->state, [Apply::APPLY_BORROW, Apply::APPLY_REDRAW]))
            return redirect()->back();
        $msg = '撤回';
        $apply->update(['state' => Apply::APPLY_REDRAW, 'review_user_id' => 0]);
        OperateLogHelper::createOperateLog(OperateLogHelper::MATERIAL, $apply->id, $msg);
        return redirect()->back();
    }

    public function confirmReturn(Request $request)
    {
        /*$ids = json_decode($request->inventoryIds, true);
        Inventory::whereIn('id', $ids)->update(['inv_remain' => \DB::raw('inv_remain + 1')]);
        return redirect()->route('material.approve.index');*/
    }
}
