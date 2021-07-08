<?php

namespace frame;

class Request 
{
	public function isWin()
	{
		return strpos(php_uname(), 'Windows') !== false;
	}

	public function isPost()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	public function isGet()
	{
		return $_SERVER['REQUEST_METHOD'] == 'GET';
	}

	public function isAjax()
	{
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		} else {
			return input('is_ajax', 0);
		}
	}

	public function isMobile()
	{
		if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
			return true;
		} elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
			return true;
		} elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
			return true;
		} elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
			return true;
		} else {
			return false;
		}
	}

	public function ipost($name='', $default=null)
	{
		if (empty($name)) return $_POST;
		if (isset($_POST[$name])) {
			return $_POST[$name];
		}
		return $default;
	}

	public function iget($name='', $default=null)
	{
		if (empty($name)) return $_GET;
		if (isset($_GET[$name])) {
			return $_GET[$name];
		}
		return $default;
	}

	public function input($name='', $default=null)
	{
		$arr = array_merge($_GET, $_POST);
		if (empty($name)) return $arr;
		if (isset($arr[$name])) {
			return $arr[$name];
		}
		return $default;
	}

	public function siteUrl($url='')
	{
		return env('APP_DOMAIN').$url;
	}

	public function mediaUrl($url='', $width='')
	{
		if (!empty($width)) {
			$ext = pathinfo($url, PATHINFO_EXTENSION);
			$url = str_replace('.'.$ext, DS.$width.'.'.$ext, $url);
		}
		if (strpos($url, 'http') === false) {
			$url = env('FILE_CENTER_DOMAIN').$url;
		}
		return $url;
	}

	public function getIp()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}
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
		if (!empty($page)) {
			$str .= '-p'.$page;
		}
		return env('APP_DOMAIN').$str.(empty(env('TEMPLATE_SUFFIX')) ? '' : '.'.env('TEMPLATE_SUFFIX'));
	}

	public function getBrowser($agent='')
	{
		if (empty($agent)) {
			$agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		}
		if (empty($agent)) {
			return '未知设备';
		} else {
			if (preg_match('/MSIE/i', $agent)) {
				return 'MSIE';
			} elseif (preg_match('/Firefox/i', $agent)) {
				return 'Firefox';
			} elseif (preg_match('/Chrome/i', $agent)) {
				return 'Chrome';
			} elseif (preg_match('/Safari/i', $agent)) {
				return 'Safari';
			} elseif (preg_match('/Opera/i', $agent)) {
				return 'Opera';
			} else {
				return 'Other';
			}
		}
	}

	public function getSystem($agent='')
	{
		if (empty($agent)) {
			$agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		}
		if (empty($agent)) {
			return '未知操作系统';
		} else {
			if (preg_match('/win/i', $agent)) {
				return 'Windows';
			} elseif (preg_match('/iphone/i', $agent)) {
				return 'iPhone';
			} elseif (preg_match('/android/i', $agent)) {
				return 'Android';
			} elseif (preg_match('/mac/i', $agent)) {
				return 'MAC';
			} elseif (preg_match('/linux/i', $agent)) {
				return 'Linux';
			} elseif (preg_match('/unix/i', $agent)) {
				return 'Unix';
			} elseif (preg_match('/bsd/i', $agent)) {
				return 'BSD';
			} else {
				return 'Other';
			}
		}
	}
}