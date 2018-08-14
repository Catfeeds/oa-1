<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Components\Helper\Scope;
use Route;

class CrmController extends Controller
{
    protected $scopeClass = Scope::class;
    protected $params;

    public function init()
    {
        parent::init();
        $this->setScope();
    }

    // 设置 scope 参数
    protected function setScopeParams()
    {
        return Route::getCurrentRequest()->get('scope', []);
    }

    // 设置 scope 实例对象
    protected function setScope()
    {
        $user = \Auth::user();
        $this->params = $this->setScopeParams();

        $this->scope = new $this->scopeClass($this->params, $user);
    }

    /**
     * 响应dataTable
     * @param $data
     * @param int $recordsTotal
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($data, $recordsTotal = null)
    {
        //优先判断json是否成功
        json_encode($data);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $data = [];
            \Log::error(sprintf("Failed to parse json string: '%s', error: '%s'", json_encode($data), json_last_error_msg()));
        }

        $ret = [
            'draw' => intval(\Request::get('draw')),
            'data' => $data,
        ];

        if ($recordsTotal !== null) {
            $ret['recordsTotal'] = $ret['recordsFiltered'] = $recordsTotal;
        }

        return response()->json($ret);
    }
}
