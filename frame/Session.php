<?php 

namespace frame;

class Session
{
	public function set($name, $data=null, $key=null)
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

	public function get($name, $default=null, $key=null)
	{
		if ($key) return isset($_SESSION[$name][$key]) ? $_SESSION[$name][$key] : $default;
		return $_SESSION[$name] ?? $default;
	}

	public function del($name, $key=null)
	{
		if ($key) unset($_SESSION[$name][$key]);
		else unset($_SESSION[$name]);
		return true;
	}

	public function dGet($name, $key=null)
	{
		$rst = $this->get($name, null, $key);
		$this->del($name, $key);
		return $rst;
	}

	public function close()
	{

	}
}