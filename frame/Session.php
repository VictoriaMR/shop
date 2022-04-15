<?php 

namespace frame;

class Session
{
	public static function set($name, $data, $key=null)
	{
		if ($key) {
			if (!isset($_SESSION[$name])) {
				$_SESSION[$name] = [];
			}
			$_SESSION[$name][$key] = $data;
		} else {
			$_SESSION[$name] = $data;
		}
		return true;
	}

	public static function get($name, $default=null, $key=null)
	{
		if ($key) return isset($_SESSION[$name][$key]) ? $_SESSION[$name][$key] : $default;
		return $_SESSION[$name] ?? $default;
	}

	public static function del($name, $key=null)
	{
		if ($key) unset($_SESSION[$name][$key]);
		else unset($_SESSION[$name]);
		return true;
	}
}