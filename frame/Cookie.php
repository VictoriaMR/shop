<?php

namespace frame;

class Cookie
{
	protected array $config = [
		'expires' => 0,
		'path' => '/',
		'domain' => '',
		'secure' => false,
		'httponly' => true,
		'samesite' => 'lax',
	];

	public function __construct()
	{
		$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
			|| (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
			|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
			|| (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https');
		$this->config['secure'] = $isHttps;
	}

	public function init($home = true): bool
	{
		if (userId() > 0) {
			return true;
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
				return true;
			}
		}
		return false;
	}

	public function login($memId)
	{
		$uuid = $this->get('uuid');
		if (empty($uuid)) {
			$uuid = $this->setUuid();
		}
		$uuidService = service('member/Uuid');
		$where = [
			'uuid' => $uuid,
			'site_id' => siteId(),
		];
		if ($uuidService->getCountData($where)) {
			return $uuidService->updateData($where, ['mem_id' => $memId]);
		}
		$where['mem_id'] = $memId;
		$where['lan_cur'] = 'en_usd';
		return $uuidService->insertData($where);
	}

	public function setUuid($home = true)
	{
		$uuid = $this->get('uuid');
		if (empty($uuid)) {
			$uuid = randString(32);
			$this->set('uuid', $uuid, 3600 * 24 * 30);
			if ($home) {
				$this->set('language', 'en', 3600 * 24 * 30);
				$this->set('currency', 'usd', 3600 * 24 * 30);
			}
		}
		return $uuid;
	}

	public function updateLanguage($language): bool
	{
		return $this->set('language', $language, 3600 * 24 * 30);
	}

	public function updateCurrency($currency): bool
	{
		return $this->set('currency', $currency, 3600 * 24 * 30);
	}

	public function set($name, $value = '', $option = null): bool
	{
		if ($option === null) {
			$option = $this->config;
		} elseif (is_numeric($option)) {
			$option = ['expires' => time() + (int)$option] + $this->config;
		} elseif (is_array($option)) {
			if (isset($option['expire']) && !isset($option['expires'])) {
				$option['expires'] = time() + (int)$option['expire'];
				unset($option['expire']);
			}
			$option = $option + $this->config;
		}

		$cookieValue = is_array($value)
			? 'json:' . json_encode($value, JSON_UNESCAPED_UNICODE)
			: (string)$value;

		$_COOKIE[$name] = $cookieValue;
		return setcookie($name, $cookieValue, $option);
	}

	public function has($name): bool
	{
		return isset($_COOKIE[$name]);
	}

	public function get($name)
	{
		if (!isset($_COOKIE[$name])) {
			return null;
		}
		$value = $_COOKIE[$name];
		if (is_string($value) && str_starts_with($value, 'json:')) {
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
