<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Components\Helper\Scope;
use Route;

class CrmController extends Controller
{
    protected $scopeClass = Scope::class;
    protected $params;

    // 默认缓存 30 分钟
    const CACHE_EXPIRE = 30;

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

        //缓存key
        $key = sprintf('%s-user%d', strtolower(str_replace('Crm', '', \Route::currentRouteName())), $user->user_id);

        //判断条件
        $decide = !empty($this->params);

        //缓存数值
        $scope_array = $this->params;

        $this->params = $this->getScope($key, $decide, $scope_array);

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

    //设置和获取缓存里面的 scope
    protected function getScope($key, $decide, $scope_array)
    {
        if ($decide) {
            $this->setCache($key, json_encode($scope_array));
        } else {
            $screeningValues = $this->getCache($key);
            if ($screeningValues) {
                $this->params = $this->params + json_decode($screeningValues, true);
            }
        }

        return $this->params;
    }

    protected function setCache($key, $data)
    {
        $key = sprintf('%d-%s', \Auth::user()->user_id, $key);
        $value = \GuzzleHttp\json_encode($data);
        \Cache::put($key, $value, self::CACHE_EXPIRE);
    }

    protected function getCache($key, $assoc = false)
    {
        $key = sprintf('%d-%s', \Auth::user()->user_id, $key);
        $value = \Cache::get($key);
        if (!empty($value)) {
            try {
                return \GuzzleHttp\json_decode($value, $assoc);
            } catch (\InvalidArgumentException $e) {
                \Log::error($e->getMessage());
                return false;
            }
        }
        return false;
    }
}
