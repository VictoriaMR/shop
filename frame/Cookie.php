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
		// 如果已有登录状态，跳过 UUID 自动登录查询
		if (userId() > 0) {
			frame('Session')->set('set_cookie', true);
			return;
		}

		$uuid = $this->get('uuid');
		if ($uuid && is_string($uuid) && strlen($uuid) >= 16) {
			// 自动登录查询
			$info = service('member/Uuid')->getInfo($uuid);
			if (!empty($info['mem_id'])) {
				service('member/Member')->loginById($info['mem_id']);
				if ($home && !empty($info['lan_cur'])) {
					$arr = explode('_', $info['lan_cur']);
					if (isset($arr[0])) $this->set('language', $arr[0]);
					if (isset($arr[1])) $this->set('currency', $arr[1]);
				}
			}
		}
		frame('Session')->set('set_cookie', true);
	}

	public function setUuid($home = true)
	{
		$uuid = $this->get('uuid');
		if (empty($uuid)) {
			$exp = 3600 * 24 * 10;
			$this->set('uuid', randString(32), $exp);
			if ($home) {
				$this->set('language', 'en', $exp);
				$this->set('currency', 'usd', $exp);
			}
		}
		frame('Session')->set('set_uuid', true);
	}

	public function login($memId)
	{
		$uuidService = service('member/Uuid');
		$uuid = $this->get('uuid');
		if (empty($uuid)) {
			return false;
		}
		$where = [
			'uuid' => $uuid,
			'site_id' => siteId(),
		];
		if ($uuidService->getCountData($where)) {
			return false;
		}
		$where['mem_id'] = $memId;
		$where['lan_cur'] = 'en_usd';
		return $uuidService->insert($where);
	}

	public function updateLanguage($language)
	{
		return $this->set('language', $language);
	}

	public function updateCurrency($currency)
	{
		return $this->set('currency', $currency);
	}

	public function set($name, $value = '', $option = null)
	{
		$config = $this->config;
		$reqTime = $_SERVER['REQUEST_TIME'] ?? time();

		if ($option !== null) {
			if (is_numeric($option)) {
				$config['expires'] = $reqTime + intval($option);
			} else {
				if (is_string($option)) {
					parse_str($option, $option);
				}
				$option = array_change_key_case($option);
				$config = array_merge($config, $option);
				if (isset($option['expire']) && !isset($option['expires'])) {
					$config['expires'] = $reqTime + intval($option['expire']);
				} elseif (isset($config['expires']) && $config['expires'] > 0 && $config['expires'] < 1000000000) {
					$config['expires'] = $reqTime + intval($config['expires']);
				}
			}
		}

		if (is_array($value)) {
			$cookieValue = 'json:' . json_encode($value, JSON_UNESCAPED_UNICODE);
		} else {
			$cookieValue = (string)$value;
		}

		$_COOKIE[$name] = $cookieValue;
		frame('Session')->set(config('domain', 'class') . '_info', $value, $name);
		unset($config['expire']);

		return setcookie($name, $cookieValue, $config);
	}

	public function has($name)
	{
		return isset($_COOKIE[$name]);
	}

	public function get($name)
	{
		if (!isset($_COOKIE[$name])) {
			return null;
		}
		$value = $_COOKIE[$name];
		if (is_string($value) && strncmp($value, 'json:', 5) === 0) {
			return json_decode(substr($value, 5), true);
		}
		return $value;
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