<?php

namespace frame;

class Cookie
{
	protected $config = [
		'expires' => 0,
		'path' => '/',
		'domain' => '',
		'secure' => true,
		'httponly' => true,
		'samesite' => 'lax',
	];

	public function init($home = true)
	{
		if (userId() > 0) {
			return true;
		}
		$uuid = $this->get('uuid');
		if ($uuid) {
			// 自动登录查询
			$info = service('member/Uuid')->getInfo($uuid);
			if (!empty($info['mem_id'])) {
				service('member/Member')->loginById($info['mem_id']);
				if ($home) {
					$arr = explode('_', $info['lan_cur']);
					$this->set('language', $arr[0] ?? 'en');
					$this->set('currency', $arr[1] ?? 'usd');
				}
			}
		}
		return false;
	}

	public function set($name, $value = '', $option = null)
	{
		if ($option === null) {
			$option = $this->config;
		} else {
			$option = $option + $this->config;
		}
		if (is_array($value)) {
			$value = 'json:' . json_encode($value, JSON_UNESCAPED_UNICODE);
		}
		return setcookie($name, $value, $option);
	}

	public function get($name)
	{
		if (isset($_COOKIE[$name])) {
			$value = $_COOKIE[$name];
			if (strncmp($value, 'json:', 5) === 0) {
				return json_decode(substr($value, 5), true);
			} else {
				return $value;
			}
		}
		return null;
	}

	public function clear()
	{
		$uuid = $this->get('uuid');
		if ($uuid) {
			return service('member/Uuid')->updateData(['uuid' => $uuid], ['mem_id' => 0]);
		}
		return false;
	}
}
