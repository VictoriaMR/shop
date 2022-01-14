<?php 

namespace frame;

class Session
{
	public static function set($name, $data=null)
	{
		if (is_null($data)) {
			$_SESSION[$name] = null;
		} else {
			$_SESSION[$name] = $data;
		}
		return true;
	}

	public static function get($name, $key=null, $default=null)
	{
		if (empty($key)) return $_SESSION[$name] ?? $default;
		return $_SESSION[$name][$key] ?? $default;
	}
}