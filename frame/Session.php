<?php 

namespace frame;

class Session
{
	public static function set($name=null, $data=null)
	{
		if (is_null($name)) {
			$_SESSION = null;
		} else {
			if (is_null($data)) {
				unset($_SESSION[$name]);
			} else {
				$temp = explode('.', $name);
				if (count($temp) == 1) {
					$_SESSION[$name] = $data;
				} else {
					if (!isset($_SESSION[$temp[0]])) {
						$_SESSION[$temp[0]] = [];
					}
					$_SESSION[$temp[0]][$temp[1]] = $data;
				}
			}
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