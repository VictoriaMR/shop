<?php

namespace frame;

class Time
{
    // 获取运行环境默认时区
	public function defaultTimeZone()
	{
		return date_default_timezone_get();
	}

    /**
     * 时区转换函数
     * @param string|int $date 待转换时间字符串, 时间字串或者时间戳
     * @param \DateTimeZone|string $toZone 目的时区
     * @param string $format 输出格式
     * @param \DateTimeZone|string $fromZone 来源时区
     * @return string
     */
	public function timeZoneConvert($toZone, $format = 'Y-m-d H:i:s', $date = null, $fromZone = null)
    {
        if(is_null($fromZone)) $fromZone = $this->defaultTimeZone();
        if(is_string($fromZone)) $fromZone = new \DateTimeZone($fromZone);
        if(is_string($toZone)) $toZone = new \DateTimeZone($toZone);
        if(is_null($date)) $date = time();
        if(is_numeric($date)) $date = '@'.$date;
        $dateTime = new \DateTime($date,$fromZone);
        $dateTime->setTimezone($toZone);
        return $dateTime->format($format);
    }

    // 获取UTC时间
    public function getUTC($format = 'Y-m-d H:i:s', $date = null, $fromZone = null)
    {
        return $this->timeZoneConvert('UTC',$format,$date, $fromZone);
    }

    // 获取ISO8601格式时间， 很多外部接口会使用此时间格式， 实际是转化为UTC标准时， 添加了T,Z符号
    public function getISO8601Time($date = null, $fromZone = null)
    {
        return $this->timeZoneConvert('UTC','Y-m-d\TH:i:s\Z',$date, $fromZone);
    }

    // 将ISO8601格式时间转化为其他指定时区，格式的时间字符串
    public function iso8601ToTime($iso8601, $toZone = null, $format = 'Y-m-d H:i:s')
    {
        if(is_null($toZone)) $toZone = $this->defaultTimeZone();
        return $this->timeZoneConvert($toZone,$format, $iso8601, 'UTC');
    }

    // 两个日期相差的天数, $date2为null 时候计算$date1与今天相差的天数
    public function countDays($date1, $date2=null)
    {
        if(is_null($date2)) $date2 = now();
        $date1 = strtotime(date('Y-m-d',strtotime($date1)));
        $date2 = strtotime(date('Y-m-d',strtotime($date2)));
        return (int)round(abs($date1-$date2)/86400);
    }
}