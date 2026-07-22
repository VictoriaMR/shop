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

	public function init($home=true)
	{
		$uuid = $this->get('uuid');
		if ($uuid) {
			//自动登录
			$info = service('member/Uuid')->getInfo($uuid);
			if (!empty($info['mem_id'])) {
				service('member/Member')->loginById($info['mem_id']);
				if ($home && $info['lan_cur']) {
					$arr = explode('_', $info['lan_cur']);
					$this->set('language', $arr[0]);
					$this->set('currency', $arr[1]);
				}
			}
		}
		frame('Session')->set('set_cookie', true);
	}

	public function setUuid($home=true)
	{
		$uuid = $this->get('uuid');
		if (empty($uuid)) {
			$exp = 3600*24*10;
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
		$where = [
			'uuid' => $this->get('uuid'),
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

	public function set($name, $value='', $option=null)
	{
		if ($option !== null) {
			$config = $this->config;
			if (is_numeric($option)) {
				$config['expires'] = $_SERVER['REQUEST_TIME'] + intval($option);
			} else {
				if (is_string($option)) {
					parse_str($option, $option);
				}
				$option = array_change_key_case($option);
				$config = array_merge($config, $option);
				if (isset($option['expire']) && !isset($option['expires'])) {
					$config['expires'] = $_SERVER['REQUEST_TIME'] + intval($option['expire']);
				} elseif (isset($config['expires']) && $config['expires'] > 0 && $config['expires'] < 1000000000) {
					// Relative offset
					$config['expires'] = $_SERVER['REQUEST_TIME'] + intval($config['expires']);
				}
			}
		} else {
			$config = $this->config;
		}
		if (is_array($value)) {
			$value = 'json:' . json_encode($value, JSON_UNESCAPED_UNICODE);
		}
		$_COOKIE[$name] = $value;
		frame('Session')->set(config('domain', 'class').'_info', $value, $name);
		unset($config['expire']);
		return setcookie($name, $value, $config);
	}

	public function has($name)
	{
		return isset($_COOKIE[$name]);
	}

	public function get($name)
	{
		if (isset($_COOKIE[$name])) {
			$value = $_COOKIE[$name];
			if (strpos($value, 'json:') === 0) {
				$value = substr($value, 5);
				$value = json_decode($value, true);
			}
			return $value;
		} else {
			return null;
		}
	}

	public function clear()
	{
		// 清除登录状态
		return service('member/Uuid')->updateData(['uuid' =>$this->get('uuid')], ['mem_id'=>0]);
	}
}