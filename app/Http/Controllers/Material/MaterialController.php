<?php

namespace App\Http\Controllers\Material;

use App\Components\AttendanceService\Operate\Material;
use App\Http\Components\Helpers\AttendanceHelper;
use App\Http\Components\Helpers\OperateLogHelper;
use App\Models\Attendance\Leave;
use App\Models\Material\Apply;
use App\Models\Sys\Dept;
use App\Models\Sys\Inventory;
use App\Models\Sys\OperateLog;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function applyIndex()
    {
        $title = '资质外借申请';
        $expectReturn = [];
        $inventory = Inventory::where('is_show', Inventory::STATUS_ENABLE)->get();
        $zeroInventory = Inventory::where(['is_show' => Inventory::STATUS_ENABLE, 'inv_remain' => 0])->with('apply')->get();
        foreach ($zeroInventory as $value) {
            $apply = $value->apply()->whereIn('state', [
                Apply::APPLY_SUBMIT, Apply::APPLY_REVIEW, Apply::APPLY_BORROW
            ])
                ->orderBy('expect_return_time', 'asc')->first();
            $expectReturn[$value->id] = $apply->expect_return_time ?? NULL;
        }
        $data = Apply::where(['user_id' => \Auth::user()->user_id])->with('inventory')->orderBy('created_at', 'desc')->take(5)->get()->toArray();
        $applies = $this->handleData($data);
        return view('material.apply', compact('title', 'inventory', 'applies', 'expectReturn'));
    }

    public function showAllApply()
    {
        $data = Apply::where(['user_id' => \Auth::user()->user_id])->with('inventory')->orderBy('created_at', 'desc')->get()->toArray();
        $applies = $this->handleData($data);
        $title = '所有记录';
        return view('material.apply-all', compact('data', 'applies', 'title'));
    }

    public function applyCreate(Request $request)
    {
        $title = '资质借用申请确认';
        $ids = $request->input('inventoryIds');
        $inventory = Inventory::whereIn('id', json_decode($ids, true))->get();
        return view('material.apply-edit', compact('title', 'inventory', 'ids'));
    }

    public function applyStore(Request $request)
    {
        $inventoryIds = json_decode($request->inventory_ids, true);
        $appInvData = [];
        //申请单检验
        $check = app(Material::class)->checkLeave($request);
        if (!$check['success']) return redirect()->back()->with('apply', 1)->withInput()->withErrors($check['message']);

        //获取申请单审核步骤流程
        $step = app(Material::class)->getLeaveStep();
        if (!$step['success']) return redirect()->back()->withInput()->withErrors($step['message']);

        $data = $request->all();
        $data['user_id'] = \Auth::user()->user_id;
        $data['annex'] = AttendanceHelper::setAnnex($request);
        $leave = array_merge($data, $step['data']);

        \DB::beginTransaction();
        try {
            $matApply = Apply::create($leave);
            OperateLogHelper::createOperateLog(OperateLogHelper::MATERIAL, $matApply->id, Apply::$stateChar[Apply::APPLY_SUBMIT]);
            $matApply->inventory()->attach($inventoryIds);
            Inventory::whereIn('id', $inventoryIds)->update(['inv_remain' => \DB::raw('inv_remain - 1')]);
        } catch (\Exception $exception) {
            \DB::rollBack();
            flash('申请失败', 'danger');
            return redirect()->route('material.apply.index');
        }
        \DB::commit();
        flash('申请成功', 'success');
        return redirect()->route('material.apply.index');
    }

    public function optInfo($id, $type)
    {
        $title = '申请单明细';
        $apply = Apply::with('inventory')->find($id);
        if (empty($apply)) return redirect()->back();
        $apply = $apply->toArray();
        $dept = Dept::getDeptList();
        $user = User::with(['dept'])->where(['user_id' => $apply['review_user_id']])->first();
        $logs = OperateLog::where(['type_id' => OperateLogHelper::MATERIAL, 'info_id' => $id])->get();
        $back = $type == Leave::LOGIN_VERIFY_INFO ? route('material.approve.index', ['state' => 'all']) : route('material.apply.index');
        return view('material.apply-info', compact('title', 'apply', 'dept', 'user', 'logs', 'back', 'type'));
    }

    public function approveIndex($state)
    {
        $title = '资质外借审批';
        $optNames = array_values(Apply::$stateChar);
        $status = $state == 'all' ? array_keys(Apply::$stateChar) : [$state];
        $ids = OperateLogHelper::getLogInfoIdToUid(\Auth::user()->user_id, OperateLogHelper::MATERIAL, $optNames);
        $data = Apply::where(function ($query) use ($ids) {
            $query->whereIn('id', $ids)->orWhereRaw('review_user_id = ' . \Auth::user()->user_id);
        })
            ->whereIn('state', $status)
            ->orderBy('created_at', 'desc')->with('inventory')->paginate(30)->toArray();
        $applies = $this->handleData($data['data'], ['name', 'type', 'company']);
        return view('material.approve', compact('title', 'applies', 'state'));
    }

    public function handleData($applies, array $types = ['name'])
    {
        $typeArr = [];
        foreach ($applies as $key => $apply) {
            foreach ($apply['inventory'] as $inv) {
                foreach ($types as $type) {
                    $typeArr["$key--$type"] = $typeArr["$key--$type"] ?? '';
                    $typeArr["$key--$type"] = $typeArr["$key--$type"] . '/' . $inv[$type];
                }
            }
        }
        foreach ($typeArr as $key => $value) {
            $index = explode('--', $key);
            $applies[$index[0]]['inventory_' . $index[1]] = trim($value, '/');
        }
        return $applies;
    }

    public function reviewOptStatus(Request $request, $id)
    {
        $status = (int)$request->status;
        if (!in_array($status, array_keys(Apply::$stateChar)) || empty($id)) {
            flash('操作失败', 'danger');
            return redirect()->back();
        }
        $optStatus = $this->OptStatus($id, $status, $request);
        $optStatus ? flash('操作成功', 'success') : flash('操作失败', 'danger');
        return redirect()->back();
    }

    public function OptStatus($id, $status, $request)
    {
        $apply = Apply::with('inventory')->findOrFail($id);

        if ($apply->status === $status) return false;

        if (empty($apply->id) || $apply->review_user_id != \Auth::user()->user_id) {
            return false;
        }

        $msg = '';
        //mysql事物开始
        \DB::beginTransaction();
        try {
            switch ($status) {
                //拒绝通过状态
                case Apply::APPLY_FAIL:
                    $msg = Apply::$stateChar[Apply::APPLY_FAIL];
                    $apply->update(['state' => Apply::APPLY_FAIL, 'review_user_id' => 0]);
                    $apply->inventory()->update(['inv_remain' => \DB::raw('inv_remain + 1')]);
                    break;
                //审核通过状态
                case Apply::APPLY_PASS:
                    $msg = Apply::$stateChar[Apply::APPLY_PASS];
                    //申请单状态操作
                    $driver = 'material';
                    \AttendanceService::driver($driver)->leaveReviewPass($apply);
                    break;
                //申请取消
                case Apply::APPLY_CANCEL:
                    $msg = Apply::$stateChar[Apply::APPLY_CANCEL];
                    $apply->update(['state' => Apply::APPLY_CANCEL, 'review_user_id' => 0]);
                    $apply->inventory()->update(['inv_remain' => \DB::raw('inv_remain + 1')]);
                    break;
                //确认物资归还
                case Apply::APPLY_RETURN:
                    if (empty($request->inventoryIds)) throw new \Exception('error');
                    $ids = json_decode($request->inventoryIds, true);
                    $invIds = $apply->inventory()->wherePivot('part', 0)->get()->pluck('id')->toArray();
                    $diffIds = array_diff($invIds, $ids);
                    Inventory::whereIn('id', $ids)->update(['inv_remain' => \DB::raw('inv_remain + 1')]);
                    if (empty($diffIds)) {
                        $msg = Apply::$stateChar[Apply::APPLY_RETURN];
                        $apply->update(['state' => Apply::APPLY_RETURN, 'review_user_id' => 0]);
                    }else {
                        $msg = Apply::$stateChar[Apply::APPLY_PART_RETURN];
                        $apply->update(['state' => Apply::APPLY_PART_RETURN]);
                    }
                    $apply->inventory()->updateExistingPivot(array_values($ids), ['part' => 1]);
                    break;
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
        $apply = Apply::with('inventory')->findOrFail($id);
        if (in_array($apply->state, [Apply::APPLY_BORROW, Apply::APPLY_REDRAW]))
            return redirect()->back();
        \DB::beginTransaction();
        try {
            $msg = Apply::$stateChar[Apply::APPLY_REDRAW];
            $apply->update(['state' => Apply::APPLY_REDRAW, 'review_user_id' => 0]);
            OperateLogHelper::createOperateLog(OperateLogHelper::MATERIAL, $apply->id, $msg);
        } catch (\Exception $ex) {
            \DB::rollback();
            return redirect()->back();
        }
        \DB::commit();
        return redirect()->back();
    }
}

