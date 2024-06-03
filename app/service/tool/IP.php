<?php
namespace app\service\tool;

use \MaxMind\Db\Reader;

class IP
{
    const DB_PATH = ROOT_PATH.'MaxMind'.DS.'GeoLite2-City.mmdb';
    const DB_DEFAULT_LANGUAGE = 'en';

    public function isIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    public function isIpv4($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    public function isIpv6($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    public function ipToBin($ip)
    {
        $len = self::isIpv6($ip)?16:4;
        $bin_str = inet_pton($ip);
        $bin = '';
        foreach(str_split($bin_str) as $char){
            $bin .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }
        return $bin;
    }

    public function checkIp($ip, $cidr)
    {
        if (!strpos($cidr, '/')) {
            $len = $this->isIpv6($ip)?128:32;
            $cidr .='/'.$len;
        }
        list ($subnet, $bits) = explode('/', $cidr);
        $ip_bin = substr($this->ipToBin($ip),0,$bits);
        $subnet_bin = substr($this->ipToBin($subnet),0,$bits);
        return $ip_bin == $subnet_bin;
    }

    public function getInfo($ip)
    {
        if (strpos($ip,'/')) {
            $ip = explode('/',$ip);
            $ip = $ip[0];
        }
        if(!$this->isIp($ip)) {
            return false;
        }
        $ip = '66.249.65.172';
        $cacheKey = 'IP:'.$ip;
        $data = redis(4)->get($cacheKey);
        if (empty($data)) {
            $data = $this->maxMindInstance()->get($ip);
            if (empty($data)) {
                return false;
            }
        }
        if (!empty($data)) {
            return $data;
        }
        $returnData = [];
        // 洲
        if (isset($data['continent'])) {
            $returnData['continent_code'] = isset($data['continent']['code'])?$data['continent']['code']:'';
            $returnData['continent'] = isset($data['continent']['names'][lanId('code')])?$data['continent']['names'][lanId('code')]:$data['continent']['names'][self::DB_DEFAULT_LANGUAGE];
        }
        // 国家
        if (isset($data['country'])) {
            $returnData['country_code'] = isset($data['country']['iso_code'])?$data['country']['iso_code']:'';
            $returnData['country'] = isset($data['country']['names'][lanId('code')])?$data['country']['names'][lanId('code')]:$data['country']['names'][self::DB_DEFAULT_LANGUAGE];
        }
        // 州
        if (isset($data['subdivisions'])) {
            $tmp = array_pop($data['subdivisions']);
            $returnData['state_code'] = isset($tmp['iso_code'])?$tmp['iso_code']:'';
            $returnData['state'] = isset($tmp['names'][lanId('code')])?$tmp['names'][lanId('code')]:$tmp['names'][self::DB_DEFAULT_LANGUAGE];
        }
        // 城市
        if (isset($data['city'])) {
            $returnData['city'] = isset($data['city']['names'][lanId('code')])?$data['city']['names'][lanId('code')]:$data['city']['names'][self::DB_DEFAULT_LANGUAGE];
        }
        // 邮编
        $returnData['postal'] = isset($data['postal'])?$data['postal']['code']:'';
        // 定位
        if (isset($data['location'])) {
            $returnData['accuracy_radius'] = isset($data['location']['accuracy_radius'])?$data['location']['accuracy_radius']:null;
            $returnData['latitude'] = isset($data['location']['latitude'])?$data['location']['latitude']:null;
            $returnData['longitude'] = isset($data['location']['longitude'])?$data['location']['longitude']:null;
            $returnData['time_zone'] = isset($data['location']['time_zone'])?$data['location']['time_zone']:null;
        }
        redis(4)->set($cacheKey, $returnData, 24*3600);
        return $returnData;
    }

    public function getCountryCode()
    {
        if(isset($_SERVER['HTTP_CF_IPCOUNTRY'])){
            $code2 = $_SERVER['HTTP_CF_IPCOUNTRY'];
        }else{
            $code2 = $this->getInfo(request()->getIp())['country_code'] ?? '';
        }
        if(empty($code2)){
            $code2 = 'US';
        } else {
            $code2 = strtoupper($code2);
        }
        return $code2;
    }

    private function maxMindInstance()
    {
        if(empty($this->maxMindInstance)) {
            $this->maxMindInstance = new Reader(self::DB_PATH);
        }
        return $this->maxMindInstance;
    }
}