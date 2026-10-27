<?php 

namespace frame;

class Session
{
	private $_started = false;

	private function ensureStarted()
	{
		if (!$this->_started) {
			if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
				session_cache_limiter('');
				header('Cache-Control: private, no-cache, must-revalidate');
				@session_start();
			}
			$this->_started = (session_status() === PHP_SESSION_ACTIVE);
		}
	}

	public function set($name, $data = null, $key = null)
	{
		$this->ensureStarted();
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
		if (!$this->_started && empty($_COOKIE[session_name()])) {
			return $default;
		}
		$this->ensureStarted();
		if (!isset($_SESSION)) {
			return $default;
		}
		if ($key !== null) {
			return $_SESSION[$name][$key] ?? $default;
		}
		return $_SESSION[$name] ?? $default;
	}

	public function del($name, $key = null)
	{
		if (!$this->_started && empty($_COOKIE[session_name()])) {
			return true;
		}
		$this->ensureStarted();
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
		$this->_started = false;
	}
}
