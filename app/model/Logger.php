<?php

namespace app\model;
use app\model\Base;

class Logger extends Base
{
	protected $_connect = 'static';
	protected $_table = 'visitor_log';
	protected $_primaryKey = 'log_id';

	const TYPE_LOGIN = 0;
	const TYPE_LOGOUT = 1;
	const TYPE_LOGIN_FAIL = 2;
	const TYPE_BEHAVIOR = 3;

	public function addLog(array $data)
	{
		$data['site_id'] = siteId();
		$data['mem_id'] = userId();
		$data['lan_id'] = lanId();
		$data['is_moblie'] = IS_MOBILE ? 1 : 0;
		$data['browser'] = request()->getBrowser();
		$data['system'] = request()->getSystem();
		$data['agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
		if (!isset($data['path'])) {
			$data['path'] = implode('/', \App::get('router'));
		}
		$data['ip'] = request()->getIp();
		$data['add_time'] = now();
		return $this->insert($data);
	}
}