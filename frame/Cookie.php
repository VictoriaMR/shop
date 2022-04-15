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

	public function init()
	{
		$uuid = $this->get('uuid');
		if (empty($uuid)) {
			$this->set('uuid', randString(32), 3600*24*10);
		} else {
			//自动登录
			$info = make('app/service/member/Uuid')->getInfo($uuid);
			if (!empty($info['mem_id'])) {
				if (substr($info['mem_id'], 0, 1) == 5) {
					$memberService = make('app/service/admin/Member');
				} else {
					$memberService = make('app/service/Member');
				}
				$memberService->loginById($info['mem_id']);
				session()->set('site_language_id', $info['lan_id']);
			}
			session()->set('setcookie', true);
		}
	}

	public function login($memId)
	{
		$uuidService = make('app/service/member/Uuid');
		$where = [
			'uuid' => $this->get('uuid'),
			'site_id' => siteId(),
		];
		if ($uuidService->getCountData($where)) {
			return false;
		}
		$where['mem_id'] = $memId;
		$where['lan_id'] = lanId();
		return $uuidService->insert($where);
	}

	public function updateLanguage()
	{
		return make('app/service/member/Uuid')->updateData($this->get('uuid'), ['lan_id'=>lanId()]);
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
		session()->set(APP_TEMPLATE_TYPE.'_info', $value, $name);
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
		if (empty($_COOKIE)) {
			return false;
		}
		session()->set('setcookie', false);
		make('app/service/member/Uuid')->deleteData($this->get('uuid'));
		$config = $this->config;
		foreach ($_COOKIE as $key => $val) {
			setcookie($key, '', $_SERVER['REQUEST_TIME'] - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
		}
		$_COOKIE = [];
		return true;
	}
}