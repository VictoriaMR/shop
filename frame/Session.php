<?php 

namespace frame;

class Session
{
	public static function set($key='', $data=null)
	{
		$_SESSION[$key] = $data;
		return true;
	}

	public static function get($name='')
	{
		if (empty($name)) return $_SESSION;
		return $_SESSION[$name] ?? null;
	}
}