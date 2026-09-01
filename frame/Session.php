<?php 

namespace frame;

class Session
{
	public function set($name, $data = null, $key = null)
	{
		if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
			@session_start();
		}
		if ($key !== null) {
			if (!isset($_SESSION[$name]) || !is_array($_SESSION[$name])) {
				$_SESSION[$name] = [];
			}
			$_SESSION[$name][$key] = $data;
		} else {
			$_SESSION[$name] = $data;
		}
		return true;
	}

	public function get($name, $default = null, $key = null)
	{
		if (!isset($_SESSION)) {
			return $default;
		}
		if ($key !== null) {
			return isset($_SESSION[$name][$key]) ? $_SESSION[$name][$key] : $default;
		}
		return $_SESSION[$name] ?? $default;
	}

	public function del($name, $key = null)
	{
		if (!isset($_SESSION)) {
			return true;
		}
		if ($key !== null) {
			unset($_SESSION[$name][$key]);
		} else {
			unset($_SESSION[$name]);
		}
		return true;
	}

	public function dGet($name, $key = null)
	{
		$rst = $this->get($name, null, $key);
		$this->del($name, $key);
		return $rst;
	}

	public function close()
	{
		if (session_status() === PHP_SESSION_ACTIVE) {
			session_write_close();
		}
	}
}