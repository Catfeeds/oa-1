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
     * @param $startTime
     * @param $startId
     * @param $endTime
     * @param $endId
     * @return number
     */
    public static function leaveDayDiff($startTime, $startId, $endTime, $endId)
    {
        $startTime = new \DateTime($startTime);
        $endTime = new \DateTime($endTime);

        $day = $startTime->diff($endTime);
        $days[] = (int)$day->format('%a');

        $startId = (int)str_replace(':', '', $startId);
        $endId = (int)str_replace(':', '', $endId);

        if(($endId - $startId) > 800) {
            $days[] = 1;
        } elseif($endId - $startId <= 100) {
            $days[] = 0.1;
        } else {
            $days[] = 0.5;
        }

        return array_sum($days);
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
    public static function dateTimeAdd($date, $interval, $format = 'Y-m-d H:i:s', $method = 'add')
    {
        return (new \DateTime($date))
            ->$method(new \DateInterval('P' . $interval))
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

    public static function ifBetween($start, $end, $needleStart, $sign = '')
    {
        switch ($sign) {
            case '=':
                return $needleStart >= $start && $needleStart <= $end ?  true :  false;
            case 'l=':
                return $needleStart >= $start && $needleStart < $end ? true : false;
            case 'r=':
                return $needleStart > $start && $needleStart <= $end ? true : false;
            default:
                return $needleStart > $start && $needleStart < $end ? true : false;
        }
    }

    public static function timeDiff($startTime, $endTime)
    {
        $start = new \DateTime($startTime);
        $end = new \DateTime($endTime);
        $day = $start->diff($end);
        if ((int)$day->format('%a') >= 1) {
            return date('Y-m-d', strtotime($startTime));
        }
        return (int)$day->format('%h').'小时前';
    }

    /**
     *  根据身份证号码获取生日
     *  @param string $idCard 身份证号码
     *  @return $birthday
     */
    public static function getBirthdayToIdCard($idCard)
    {
        if(empty($idCard)) return null;
        $bir = substr($idCard, 6, 8);
        $year = (int)substr($bir, 0, 4);
        $month = substr($bir, 4, 2);
        $day = substr($bir, 6, 2);
        return $year . "-" . $month . "-" . $day;
    }

    /**
     *  根据身份证号码计算年龄
     *  @param string $idCard 身份证号码
     *  @return int $age
     */
    public static function getAgeToIdCard($idCard)
    {
        if(empty($idCard)) return null;
        #  获得出生年月日的时间戳
        $date = strtotime(substr($idCard, 6, 8));
        #  获得今日的时间戳
        $today = strtotime('today');
        #  得到两个日期相差的大体年数
        $diff = floor(($today-$date)/86400/365);
        #  strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
        $age = strtotime(substr($idCard, 6, 8).' +'.$diff.'years') > $today ? ($diff+1) : $diff;
        return $age;
    }
}
