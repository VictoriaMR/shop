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
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		} else {
			return $this->input('is_ajax', false);
		}
	}

	public function isMobile()
	{
		if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], 'wap')) return true;
		if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) return true;
		if (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), 'V ND.WAP.WML')) return true;
		if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up\.browser|up\.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) return true;
		return false;
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
		if (strpos($agent, 'Chrome') !== false) return 'Chrome';
		if (strpos($agent, 'Safari') !== false) return 'Safari';
		if (strpos($agent, 'MSIE') !== false) return 'MSIE';
		if (strpos($agent, 'Firefox') !== false) return 'Firefox';
		if (strpos($agent, 'Opera') !== false) return 'Opera';
		return 'Other';
	}

	public function getSystem($agent='')
	{
		if (empty($agent)) $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		if (empty($agent)) return '未知操作系统';
		if (strpos($agent, 'android') !== false) return 'Android';
		if (strpos($agent, 'iphone') !== false) return 'iPhone';
		if (strpos($agent, 'win') !== false) return 'Windows';
		if (strpos($agent, 'mac') !== false) return 'MAC';
		if (strpos($agent, 'linux') !== false) return 'Linux';
		if (strpos($agent, 'unix') !== false) return 'Unix';
		if (strpos($agent, 'bsd') !== false) return 'BSD';
		return 'Other';
	}
}