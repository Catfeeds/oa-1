<?php
/**
 * User: xiaoqing Email: liuxiaoqing437@gmail.com
 * Date: 2017/3/28
 * Time: 下午5:21
 * gm模块辅助类
 */

namespace App\Http\Components\Helpers;


class CrmHelper
{
    public static function addEmptyToArray($allName, $array)
    {
        return ['' => $allName] + $array;
    }

    public static function percentage($number, $model = false)
    {
        if ($model) {
            return (int)$number / 100;
        } else {
            return round(($number * 100),2) . '%';
        }
    }

    public static function dividedInto($channel_rate, $first_division, $second_division, $second_division_condition, $water)
    {
        $first_division = $first_division != 0 ? $first_division : 1;
        if ($water > $second_division_condition && $second_division_condition != 0) {
            $first = $second_division_condition * (1 - $channel_rate) * $first_division;
            $second = ($water - $second_division_condition) * (1 - $channel_rate) * $second_division;
            $divided = $first + $second;
        } else {
            $divided = $water * (1 - $channel_rate) * $first_division;
        }

        return round($divided,2);
    }
}