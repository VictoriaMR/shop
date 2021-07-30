<?php 

namespace frame;

class Session
{
	public static function set($name=null, $data=null)
	{
		if (is_null($name)) {
			$_SESSION = null;
		} else {
			$_SESSION[$name] = $data;
		}
		return true;
	}

	public static function get($name='', $default=null)
	{
		if (empty($name)) return $_SESSION;
		$name = explode('.', $name);
		$data = $_SESSION;
		foreach ($name as $value) {
			if (isset($data[$value])) {
				$data = $data[$value];
			} else {
				return $default;		
			}
		}
		return $data;
	}
}