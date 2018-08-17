<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2017/11/18
 * Time: 10:48
 */
namespace App\Http\Components\BackstageApi;

use GuzzleHttp\Client;

class BackstageApi
{
    /**
     * 通知企业微信消息
     * @param array $query 请求参数
     * @return mixed
     */
    public function sendWXMsg(array $query, $type = 'push')
    {
        $url = $this->getUrl($type, 'push');

        return $this->request('GET', $url, [
            'query' => $query
        ]);
    }

    /**
     * 请求基础方法
     * @param string $method
     * @param string $url
     * @param array $options
     * @return array
     */
    private function request(string $method, string $url, array $options = [])
    {
        $client = new Client([
            'timeout' => 30,
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
                return json_decode($result, true);
            }
        } catch (\Exception $e) {
            return $this->setResult(false, '请求异常：' . $e->getMessage());
        }
    }

    /**
     * 获取后台地址
     * @param string $type
     * @param string $api
     * @return string
     */
    private function getUrl(string $type, string $api)
    {
        switch ($type) {
            case 'push':
                return 'http://oauthcenter.shiyuegame.com/' . $api;
                break;
            default:
                return 'http://oauthcenter.shiyuegame.com/' . $api;
        }
    }

    /**
     * 格式话返回
     * @param bool $success
     * @param $message
     * @return array
     */
    private function setResult($success = true, $message)
    {
        return compact('success', 'message');
    }

}