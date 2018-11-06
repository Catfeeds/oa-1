<?php

namespace App\Components\Helper;

class DataHelper
{
    public static function asPercent($num, $denominator, $decimals = 4)
    {
        $percent = $denominator != 0 ? round($num / $denominator, $decimals) : 0;
        return $percent * 100 . '%';
    }

    public static function asDate($time = null, $default = '')
    {
        is_null($time) && $time = time();
        return $time == 0 ? $default : date('Y-m-d', $time);
    }

    public static function asDateTime($time = null, $default = '')
    {
        is_null($time) && $time = time();
        return $time == 0 ? $default : date('Y-m-d H:i:s', $time);
    }

    public static function dateInterval($dateTime, $count, $before = true)
    {
        $time = strtotime($dateTime);
        if ($before) {
            $time -= 86400 * $count;
        } else {
            $time += 86400 * $count;
        }

        return self::asDateTime($time);
    }

    public static function simple($num)
    {
        if (is_numeric($num)) {
            if (strlen((int)$num) >= 5 && strlen((int)$num) < 8) {
                return self::format(sprintf('%.2f万', ($num / 10000)), $num);
            } elseif (strlen($num) >= 8) {
                return self::format(sprintf('%.2f亿', ($num / 100000000)), $num);
            } else {
                return self::format('', $num);
            }
        } else {
            return $num;
        }
    }

    public static function format($rate, $num)
    {
        return sprintf('<font title="%s">%s</font>', $rate, $num);
    }

    public static function week($date)
    {
        $weekarray = array("日", "一", "二", "三", "四", "五", "六");
        return $weekarray[date("w", strtotime($date))];
    }

    public static function hours()
    {
        $hours = [];
        foreach (range(0, 23) as $v) {
            $hours[$v] = sprintf('%d时', $v);
        }
        return $hours;
    }

    /**
     * 秒转 时间格式
     * @param $seconds //秒数
     * @return string
     */
    public static function timeToSecond($seconds)
    {
        $days_num = '';
        $seconds = abs((int)$seconds);
        if ($seconds > 3600) {
            if ($seconds > 24 * 3600) {
                $days = (int)($seconds / 86400);
                $days_num = $days . "天";
                $seconds = $seconds % 86400;//取余
            }
            $hours = intval($seconds / 3600);
            $minutes = $seconds % 3600;//取余下秒数
            $time = $days_num . $hours . "小时" . gmstrftime('%M分钟%S秒', $minutes);
        } else {
            $time = gmstrftime('%H小时%M分钟%S秒', $seconds);
        }

        return $time;
    }

    public static function activeUrl(string $url): bool
    {
        try {
            $headers = get_headers($url);
            $code = substr($headers[0], 9, 3);
        } catch (\Exception $e) {
            $code = 500;
        }

        return in_array($code, [200, 301, 302]) ? true : false;
    }
    /**
     * 产生一个随机字串
     * @param int $len 指定随机字串的长度
     * @param string $scope 随机字符的取值范围
     * @return string
     */
    public static function randString($len)
    {
        $scope = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $strLen = strlen($scope) - 1;
        $string = '';
        for($i = 0; $i < $len; $i ++){
            $string .= substr($scope, mt_rand(0, $strLen), 1);
        }
        return $string;
    }

    /**
     * 请假天数
     * @param $startTime
     * @param $endTime
     * @return float|int|string
     */
    public static function diffTime($startTime, $endTime)
    {

        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);

        $day = '';

        //时间为空的时候
        if ($startTime > 946656000 && $startTime <= $endTime) {
        $day = floor($endTime - $startTime)/86400;
            switch ($day) {
                case $day > 0 && $day < 0.3 :
                    $day = 0.5;
                    break;
                case $day > 0.3 && $day < 1 :
                    $day = 1;
                    break;
                case $day > 1.1 && $day < 1.3 :
                    $day = 1.5;
                    break;
                case $day > 1.3 && $day < 2 :
                    $day = 2;
                    break;
                case $day > 2.1 && $day < 2.3 :
                    $day = 2.5;
                    break;
                case $day > 2.3 && $day < 3 :
                    $day = 3;
                    break;
                case $day > 3.1 && $day < 3.3 :
                    $day = 3.5;
                    break;
                case $day > 3.3 && $day < 4 :
                    $day = 4;
                    break;
                case $day > 4.1 && $day < 4.3 :
                    $day = 4.5;
                    break;
                case $day > 4.3 && $day < 5 :
                    $day = 4.5;
                    break;
                case $day > 5.1 && $day < 5.3 :
                    $day = 5;
                    break;
                case $day > 5.3 && $day < 6 :
                    $day = 5.5;
                    break;
                case $day > 6.1 && $day < 6.3 :
                    $day = 6;
                    break;
                case $day > 6.3 && $day < 7 :
                    $day = 6.5;
                    break;
                case $day > 7.1 && $day < 7.3 :
                    $day = 7;
                    break;
                default :
                    $day = (int)(($endTime - $startTime) / 86400);
                    break;
            }
        }
        return $day;

    }

    public static function prDates($startDay, $endDay)
    {
        $day = [];
        if(empty($startDay) || empty($endDay)) return $day;
        while ($startDay < $endDay){
            $startDay = strtotime('+1 day', $startDay);
            if($startDay == $endDay) continue;
            $day[] = $startDay;
        }
        return array_unique($day);
    }


    /**
     * 对一个 DateTime 对象加上一定量的 日、月、年、小时、分钟和秒。
     * @param $date 2018-10-27 09:00:00
     * @param $formula [0,0,0,0,0,0]
     * @param string $format Y-m-d H:i:s
     * @return string 2018-10-27 09:00:00
     */
    public static function dateTimeAddToFormula($date, $formula, $format = 'Y-m-d H:i:s')
    {
        $formula = json_decode($formula, true);
        if(empty($formula)) return '';

        return (new \DateTime($date))
            ->add(new \DateInterval('P'.$formula[0].'Y'.$formula[1].'M'.$formula[2].'DT'.$formula[3].'H'.$formula[4].'M'.$formula[5].'S'))
            ->format($format);
    }

    /**
     * 对一个 DateTime 对象加上一定量的 日、月、年、小时、分钟和秒。
     *  1Y,1M 1D,T1H, T0H1M, T0H0M1S
     * @param $date
     * @param $interval
     * @param string $format
     * @return string
     */
    public static function dateTimeAdd($date, $interval, $format = 'Y-m-d H:i:s')
    {
        return (new \DateTime($date))
            ->add(new \DateInterval('P' . $interval))
            ->format($format);
    }

    public static function dateTimeAddToModify($date, $modify, $format = 'Y-m-d H:i:s')
    {
        return (new \DateTime($date))
            ->modify($modify)
            ->format($format);
    }

    /**
     * 对一个 DateTime 对象加上一定量的 日、月、小时、分钟和秒。
     * @param $date 2018-10-27 09:00:00
     * @param $formula [0,0,0,0,0]
     * @param string $format Y-m-d H:i:s
     * @return string 2018-10-27 09:00:00
     */
    public static function dateTimeAddToNaturalCycle($date, $formula, $format = 'Y-m-d H:i:s')
    {
        $formula = json_decode($formula, true);

        return (new \DateTime($date))
            ->add(new \DateInterval('P'.$formula[0].'M'.$formula[1].'DT'.$formula[2].'H'.$formula[3].'M'.$formula[4].'S'))
            ->format($format);
    }


    /**
     * 对一个 DateTime 对象减去一定量的 日、月、年、小时、分钟和秒。
     * @param $date 2018-10-27 09:00:00
     * @param $formula [0,0,0,0,0,0]
     * @param string $format Y-m-d H:i:s
     * @return string 2018-10-27 09:00:00
     */
    public static function dateTimeSubToFormula($date, $formula, $format = 'Y-m-d H:i:s')
    {
        $formula = json_decode($formula, true);

        return (new \DateTime($date))
            ->sub(new \DateInterval('P'.$formula[0].'Y'.$formula[1].'M'.$formula[2].'DT'.$formula[3].'H'.$formula[4].'M'.$formula[5].'S'))
            ->format($format);
    }

    /**
     * 对一个 DateTime 格式化
     * @param string $date
     * @param string $format Y-m-d H:i:s
     * @return string 2018-10-27 09:00:00
     */
    public static function dateTimeFormat($date = 'now', $format = 'Y-m-d H:i:s')
    {
        return (new \DateTime($date))->format($format);
    }

    public static function timesToNum(...$times)
    {
        $arr = [];
        foreach ($times as $time) {
            $arr[] = is_string($time) ? (int)str_replace(':', '', $time) : (int)str_replace(':', '', date('H:i', $time));
        }
        return $arr;
    }
}
