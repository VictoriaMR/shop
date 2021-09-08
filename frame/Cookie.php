<?php

namespace frame;

class Cookie
{
	protected $config = [
		'expire' => 0,
		'path' => '/',
		'domain' => '',
		'secure' => true,
		'httponly' => true,
	];

	public function set($name, $value='', $option=null)
	{
		$config = $this->config;
		if (!is_null($option)) {
			if (is_numeric($option)) {
				$option = ['expire' => $option];
			} elseif (is_string($option)) {
				parse_str($option, $option);
			}
			$config = array_merge($config, array_change_key_case($option));
		}
		if (is_array($value)) {
			array_walk_recursive($value, 'self::jsonFormat', 'encode');
			$value = 'json:' . json_encode($value);
		}
		$expire = empty($config['expire']) ? 0 : $_SERVER['REQUEST_TIME'] + intval($config['expire']);
		$_COOKIE[$name] = $value;
		return setcookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
	}

	public function has($name)
	{
		return isset($_COOKIE[$name]);
	}

	public function get($name)
	{
		if (isset($_COOKIE[$name])) {
			$value = $_COOKIE[$name];
			if (strpos($value, 'json:') === 0) {
				$value = substr($value, 5);
				$value = json_decode($value, true);
				array_walk_recursive($value, 'self::jsonFormat', 'decode');
			}
			return $value;
		} else {
			return null;
		}
	}

	public function delete($name)
	{
		$config = $this->$config;
		unset($_COOKIE[$name]);
		return setcookie($name, '', $_SERVER['REQUEST_TIME'] - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
	}

	private function jsonFormat(&$val, $key, $type='encode')
	{
		if (!empty($val)) {
			$val = 'decode' == $type ? urldecode($val) : urlencode($val);
		}
	}

	public function clear()
	{
		if (empty($_COOKIE)) {
			return false;
		}
		$config = $this->$config;
		foreach ($_COOKIE as $key => $val) {
			setcookie($key, '', $_SERVER['REQUEST_TIME'] - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
		}
		$_COOKIE = [];
		return true;
	}
}