<?php

namespace App\Components\Helper;

use GeoIp2\Database\Reader;

class GeoIp
{
    public static $readers = [];

    public static function getLocation($ip = '')
    {
        $reader = self::getReader();
        try {
            $location = $reader->city($ip);
        } catch (\Exception $e) {
            return '';
        }
        if ($location->country->isoCode == 'CN') {
            return $location->mostSpecificSubdivision->names['zh-CN'];
        } else {
            return $location->country->names['zh-CN'];
        }
    }

    /**
     * 获取完整ip地址信息
     * @param $ip
     * @return string
     */
    public static function getComplete($ip)
    {
        $reader = self::getReader();
        try {
            $location = $reader->city($ip);
        } catch (\Exception $e) {
            return '本地ip或异常ip';
        }

        return sprintf('%s-%s-%s',
            $location->country->names['zh-CN'] ?? $location->country->name,
            $location->mostSpecificSubdivision->names['zh-CN'] ?? $location->mostSpecificSubdivision->name,
            $location->city->names['zh-CN'] ?? $location->city->name
        );
    }


    /**
     * @param string $dbName
     * @return Reader
     */
    public static function getReader($dbName = 'GeoLite2-City.mmdb')
    {
        if (!isset(self::$readers[$dbName])) {
            self::$readers[$dbName] = new Reader(__DIR__ . DIRECTORY_SEPARATOR . $dbName);
        }
        return self::$readers[$dbName];
    }
}