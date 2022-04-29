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
		if (empty($name)) return $_POST;
		return $_POST[$name] ?? $default;
	}

	public function iget($name='', $default=null)
	{
		if (empty($name)) return $_GET;
		return $_GET[$name] ?? $default;
	}

	public function input($name='', $default=null)
	{
		$arr = array_merge($_GET, $_POST);
		if (empty($name)) return $arr;
		return $arr[$name] ?? $default;
	}

	public function getIp()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
		if (!empty($_SERVER['REMOTE_ADDR'])) return $_SERVER['REMOTE_ADDR'];
		return '';
	}

	public function filterUrl($str, $c='', $id='', $page='')
	{
		if (empty($str)) return '';
		$str = preg_replace('/[^-A-Za-z0-9 ]/', '', $str);
		$str = preg_replace('/( ){2,}/', ' ', $str);
		$str = str_replace(' ', '-', $str);
		$str = str_replace(['---', '--'], '-', $str);
		$str = strtolower($str);
		$str .= '-'.$c.'-'.$id;
		if (!empty($page)) $str .= '-p'.$page;
		return config('env.APP_DOMAIN').$str.(empty(config('env.TEMPLATE_SUFFIX')) ? '' : '.'.config('env.TEMPLATE_SUFFIX'));
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