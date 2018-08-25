<?php
/**
 * 企业微信辅助
 */

namespace App\Http\Components\Helpers;

use GuzzleHttp\Client;

class QywxHelper
{
    public static function push($useIds, $message, $dateTime)
    {
        $sign = md5($useIds.'oU0lD8GRVpvYfYUq6ensuQtHUkwtE0o3'.$dateTime);
        $client = new Client(['timeout' => 2.0]);
        $url = sprintf('http://oauthcenter.shiyuegame.com/push?userid=%s&message=%s&sign=%s&dateTime=%d', $useIds, urlencode($message), $sign, $dateTime);
        $client->get($url);
    }
}