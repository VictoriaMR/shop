<?php

namespace frame;

class Request 
{
	public function isPost()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	public function isAjax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH'])&&stripos($_SERVER['HTTP_X_REQUESTED_WITH'], 'xmlhttprequest')!==false)||input('is_ajax', false);
	}

	public function isMobile()
	{
		return isset($_SERVER['HTTP_USER_AGENT'])&&preg_match('/(android|phone|mobile|iphone|ipod|ipad|mobi|tablet|touch|aarch64|kfapwi)/i', $_SERVER['HTTP_USER_AGENT']);
	}

	public function ipost($name='', $default=null)
	{
		return $this->format($_POST, $name, $default);
	}

	public function iget($name='', $default=null)
	{
		return $this->format($_GET, $name, $default);
	}

	public function input($name='', $default=null)
	{
		return $this->format(array_merge($_GET, $_POST), $name, $default);
	}

	private function format($arr, $name, $default)
	{
		if (!$name) return $arr;
		$index = strrpos($name, '/');
		if ($index === false) {
			$type = 's';
		} else {
			$type = substr($name, $index+1);
			$name = substr($name, 0, $index);
		}
		if (!isset($arr[$name])) return $default;
		switch ($type) {
			case 'd': //整数
				return (int)$arr[$name];
			case 'f': //浮点数
				return (float) $arr[$name];
			case 't': //时间 time
				return false === strtotime($arr[$name]) ? $default : $arr[$name];
			case 'a': //数组
				return is_array($arr[$name]) ? $arr[$name] : $default;
			case 's':
				return trim($arr[$name] ?? $default);
			default:
				return $arr[$name];
		}
	}

	public function getBrowser($agent='')
	{
		if (empty($agent)) $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		if (empty($agent)) return '未知设备';
		if (stripos($agent, 'Chrome') !== false) return 'Chrome';
		if (stripos($agent, 'Safari') !== false) return 'Safari';
		if (stripos($agent, 'MSIE') !== false) return 'Msie';
		if (stripos($agent, 'Firefox') !== false) return 'Firefox';
		if (stripos($agent, 'Opera') !== false) return 'Opera';
		return 'Other';
	}

	public function getSystem($agent='')
	{
		if (empty($agent)) $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		if (empty($agent)) return '未知操作系统';
		if (stripos($agent, 'android') !== false) return 'Android';
		if (stripos($agent, 'iphone') !== false) return 'iPhone';
		if (stripos($agent, 'win') !== false) return 'Windows';
		if (stripos($agent, 'mac') !== false) return 'Mac';
		if (stripos($agent, 'linux') !== false) return 'Linux';
		if (stripos($agent, 'unix') !== false) return 'Unix';
		if (stripos($agent, 'bsd') !== false) return 'Bsd';
		return 'Other';
	}
}