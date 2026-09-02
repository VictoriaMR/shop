<?php

namespace frame;

class Request 
{
	private $_input;
	private $_isAjax;
	private $_isMobile;
	private $_isPost;

	public function isPost()
	{
		if ($this->_isPost === null) {
			$this->_isPost = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
		}
		return $this->_isPost;
	}

	public function isAjax()
	{
		if ($this->_isAjax === null) {
			$this->_isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && stripos($_SERVER['HTTP_X_REQUESTED_WITH'], 'xmlhttprequest') !== false) || (bool)$this->input('is_ajax', false);
		}
		return $this->_isAjax;
	}

	public function isMobile()
	{
		if ($this->_isMobile === null) {
			$agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
			$this->_isMobile = $agent !== '' && (bool)preg_match('/(android|phone|mobile|iphone|ipod|ipad|mobi|tablet|touch|aarch64|kfapwi)/i', $agent);
		}
		return $this->_isMobile;
	}

	public function ipost($name = '', $default = null)
	{
		return $this->format($_POST, $name, $default);
	}

	public function iget($name = '', $default = null)
	{
		return $this->format($_GET, $name, $default);
	}

	public function input($name = '', $default = null)
	{
		if ($this->_input === null) {
			if (empty($_POST)) {
				$this->_input = $_GET;
			} elseif (empty($_GET)) {
				$this->_input = $_POST;
			} else {
				$this->_input = array_merge($_GET, $_POST);
			}
		}
		return $this->format($this->_input, $name, $default);
	}

	private function format($arr, $name, $default)
	{
		if ($name === '') return $arr;
		$index = strrpos($name, '/');
		if ($index === false) {
			if (!isset($arr[$name])) return $default;
			$val = $arr[$name];
			return is_string($val) ? trim($val) : $val;
		}
		
		$type = substr($name, $index + 1);
		$name = substr($name, 0, $index);
		if (!isset($arr[$name])) return $default;
		
		switch ($type) {
			case 'd': // 整数
				return (int)$arr[$name];
			case 'f': // 浮点数
				return (float)$arr[$name];
			case 't': // 时间 time
				return strtotime($arr[$name]) === false ? $default : $arr[$name];
			case 'a': // 数组
				return is_array($arr[$name]) ? $arr[$name] : $default;
			case 's':
				return is_string($arr[$name]) ? trim($arr[$name]) : $arr[$name];
			default:
				return $arr[$name];
		}
	}

	public function getBrowser($agent = '')
	{
		if ($agent === '') $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		if ($agent === '') return '未知设备';
		if (stripos($agent, 'Chrome') !== false) return 'Chrome';
		if (stripos($agent, 'Safari') !== false) return 'Safari';
		if (stripos($agent, 'MSIE') !== false || stripos($agent, 'Trident') !== false) return 'Msie';
		if (stripos($agent, 'Firefox') !== false) return 'Firefox';
		if (stripos($agent, 'Opera') !== false || stripos($agent, 'OPR') !== false) return 'Opera';
		return 'Other';
	}

	public function getSystem($agent = '')
	{
		if ($agent === '') $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		if ($agent === '') return '未知操作系统';
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