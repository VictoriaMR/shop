<?php

namespace frame;

class Cookie
{
	protected $config = [
		'expire' => 0,
		'path' => '/',
		'domain' => '',
		'secure' => true,
		'httponly' => true,
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
					list($language, $currency) = explode('_', $info['lan_cur']);
					$this->set('language', $language, $exp);
					$this->set('currency', $currency, $exp);
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
		$config = $this->config;
		if (!is_null($option)) {
			if (is_numeric($option)) {
				$option = ['expire' => $option];
			} elseif (is_string($option)) {
				parse_str($option, $option);
			}
			$config = array_merge($config, array_change_key_case($option));
		}
		if (is_array($value)) {
			array_walk_recursive($value, 'self::jsonFormat', 'encode');
			$value = 'json:' . json_encode($value);
		}
		$expire = empty($config['expire']) ? 0 : $_SERVER['REQUEST_TIME'] + intval($config['expire']);
		$_COOKIE[$name] = $value;
		frame('Session')->set(config('domain', 'class').'_info', $value, $name);
		return setcookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
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
				array_walk_recursive($value, 'self::jsonFormat', 'decode');
			}
			return $value;
		} else {
			return null;
		}
	}

	private function jsonFormat(&$val, $key, $type='encode')
	{
		if (!empty($val)) {
			$val = 'decode' == $type ? urldecode($val) : urlencode($val);
		}
	}

	public function clear()
	{
		// 清除登录状态
		service('member/Uuid')->updateData(['uuid' =>$this->get('uuid')], ['mem_id'=>0]);
		return true;
	}
}