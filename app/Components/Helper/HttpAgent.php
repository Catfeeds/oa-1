<?php
/**
 * User: xiaoqing Email: liuxiaoqing437@gmail.com
 * Date: 2018/5/10
 * Time: 下午2:26
 * hht请求类
 */

namespace App\Components\Helper;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HttpAgent
{
    private static $instance;
    private $timeout = 5;    //5s
    private $connentTimeout = 5;    //5s

    private function __construct()
    {

    }

    /**
     * 静态工厂方法，返还此类的唯一实例
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 通用请求基础方法
     * @param string $method
     * @param string $url
     * @param array $options
     * @return array
     */
    public function request(string $method, string $url = '', array $options = []): array
    {
        $client = new Client([
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connentTimeout,
        ]);

        try {
            $response = $client->request($method, $url, $options);
            // 获取登陆后显示的页面
            $result = $response->getBody()->getContents();
            $code = $response->getStatusCode(); // 200
            $reason = $response->getReasonPhrase(); // OK

            if ($code != 200 || $reason != 'OK') {
                return $this->setResult(false, sprintf('请求失败,code:%d,reason:%s', $code, $reason));
            } else {
                return $this->setResult(true, $result);
            }
        } catch (RequestException $e) {
            \Log::info(sprintf('请求异常：%s', $e->getMessage()), ['method' => $method, 'url' => $url, 'options' => $options]);
            return $this->setResult(false, '请求异常：' . $e->getMessage());
        }
    }


    /**
     * 格式话返回
     * @param bool $success
     * @param $message
     * @return array
     */
    public function setResult($success = true, $message)
    {
        return compact('success', 'message');
    }

    public function __clone()
    {
        die('Clone is not allowed.' . E_USER_ERROR);
    }
}