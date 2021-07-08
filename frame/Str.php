<?php

namespace frame;

class Str 
{
	public function random($len) 
	{
		$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$key = '';
		for ($i=0; $i<$len; $i++) {
			$key .= $str[mt_rand(0, 32)];
		}
		return $key;
	}

	public function getUniqueName()
	{
		$arr = explode(' ', microtime());
		return str_replace('.', '', $arr[0] + $arr[1]);
	}

	public function lowerString($name)
	{
		return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
	}

	public function upperString($str, $ucfirst=false)
	{
		$str = ucwords(str_replace('_', ' ', $str));
		$str = str_replace(' ','',lcfirst($str));
		return $ucfirst ? ucfirst($str) : $str;
	}
}