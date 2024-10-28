<?php

namespace frame;

use \MaxMind\Db\Reader;

class IP
{
	const DB_PATH = ROOT_PATH.'MaxMind/GeoLite2-City.mmdb';

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
		$len = $this->isIpv6($ip)?16:4;
		$bin_str = inet_pton($ip);
		$bin='';
        foreach(str_split($bin_str) as $char){
			$bin .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
		}

		return $bin;
	}

	public function binToIp($bin)
	{
		$len=strlen($bin);
		if($len!=32 && $len!=128) { //not a vaild ip bin string
			return false;
		}
		if ($len==32) {
			$arr = str_split($bin,8);
			$arr = array_map('bindec',$arr);
			return implode('.', $arr);
		} else {
			$arr = str_split($bin,16);
			$arr = array_map('bindec',$arr);
			$arr=array_map('dechex',$arr);
			return implode(':', $arr);
		}
	}

	public function checkIp($ip, $cidr)
	{
        if (!strpos($cidr, '/')) {
            $len = $this->isIpv6($ip)?128:32;
            $cidr .='/'.$len;
        }
		list ($subnet, $bits) = explode('/', $cidr);
        
		$ip_bin=substr($this->ipToBin($ip),0,$bits);
		$subnet_bin=substr($this->ipToBin($subnet),0,$bits);

		return $ip_bin==$subnet_bin;
	}

	public function checkIpBatch($ip, $cidr_array)
	{
		foreach ($cidr_array as $v) {
			$bool = $this->checkIp($ip, $v);
			if ($bool) {
				return true;
			}
		}
		return false;
	}

	public function ipv4ToLong($ip)
	{
		$ip = ip2long($ip);
		return sprintf('%u', $ip);
	}

	public function longToIpv4($addr)
	{
		return long2ip($addr);
	}

	public function ipMaskSize($mask)
	{
		$s=$this->ipToBin($mask);
		$i=strpos($s,'0');
		if($i===false)
			$i=strlen($s);
		return $i;
	}

	public function ipRange($cidr)
	{
		list ($subnet, $bits) = explode('/', $cidr);
		$len=$this->isIpv6($subnet)?128:32;
		$subnet_prefix=substr($this->ipToBin($subnet),0,$bits);
		$result =array();
		$result['ip'] = $subnet;
		$result['ip_s'] = $this->binToIp(str_pad($subnet_prefix, $len, '0', STR_PAD_RIGHT));
		$result['ip_e'] = $this->binToIp(str_pad($subnet_prefix, $len, '1', STR_PAD_RIGHT));
		$result['mask'] = $this->binToIp(str_pad(str_repeat('1',$bits),$len,'0',STR_PAD_RIGHT));
		$result['mask_size'] = $bits;
		$result['count'] = pow(2, $len-$bits);
		return $result;
	}

	public function ipMaskRange($ip, $mask)
	{
		$mask_size=$this->ipMaskSize($mask);
		$rst=$this->ipRange($ip.'/'.$mask_size);
		return $rst;
	}

	public function getIpCountry()
	{
		$reader = new Reader(self::DB_PATH);
		$data = $reader->get($this->getIp());
		return $data['country']['iso_code'] ?? '';
	}

	public function getIp()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
		if (!empty($_SERVER['REMOTE_ADDR'])) return $_SERVER['REMOTE_ADDR'];
		return '';
	}
}