<?php

namespace frame;

class Request
{
	private ?array $_input = null;
	private ?bool $_isAjax = null;
	private ?bool $_isMobile = null;
	private ?bool $_isPost = null;

	public function isPost(): bool
	{
		if ($this->_isPost === null) {
			$this->_isPost = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
		}
		return $this->_isPost;
	}

	public function isAjax(): bool
	{
		if ($this->_isAjax === null) {
			$this->_isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && stripos($_SERVER['HTTP_X_REQUESTED_WITH'], 'xmlhttprequest') !== false) || (bool)$this->input('is_ajax', false);
		}
		return $this->_isAjax;
	}

	public function isMobile(): bool
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

	private function format(array $arr, string $name, $default)
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

		return match ($type) {
			'd' => (int)$arr[$name],
			'f' => (float)$arr[$name],
			't' => strtotime((string)$arr[$name]) === false ? $default : $arr[$name],
			'a' => is_array($arr[$name]) ? $arr[$name] : $default,
			's' => is_string($arr[$name]) ? trim($arr[$name]) : $arr[$name],
			default => $arr[$name],
		};
	}

	public function getBrowser(string $agent = ''): string
	{
		if ($agent === '') $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		if ($agent === '') return '未知设备';
		return match (true) {
			stripos($agent, 'OPR') !== false || stripos($agent, 'Opera') !== false => 'Opera',
			stripos($agent, 'Edg') !== false => 'Edge',
			stripos($agent, 'Chrome') !== false => 'Chrome',
			stripos($agent, 'Safari') !== false => 'Safari',
			stripos($agent, 'MSIE') !== false || stripos($agent, 'Trident') !== false => 'Msie',
			stripos($agent, 'Firefox') !== false => 'Firefox',
			default => 'Other',
		};
	}

	public function getSystem(string $agent = ''): string
	{
		if ($agent === '') $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		if ($agent === '') return '未知操作系统';
		return match (true) {
			stripos($agent, 'android') !== false => 'Android',
			stripos($agent, 'iphone') !== false => 'iPhone',
			stripos($agent, 'ipad') !== false => 'iPad',
			stripos($agent, 'win') !== false => 'Windows',
			stripos($agent, 'mac') !== false => 'Mac',
			stripos($agent, 'linux') !== false => 'Linux',
			stripos($agent, 'unix') !== false => 'Unix',
			stripos($agent, 'bsd') !== false => 'Bsd',
			default => 'Other',
		};
	}
}
