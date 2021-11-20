<?php

namespace app\model;
use app\model\Base;

class Logger extends Base
{
	protected $_connect = 'static';
	protected $_table = 'visitor_logger';
	protected $_primaryKey = 'log_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['log_id', 'site_id', 'mem_id', 'type', 'is_moblie'];

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
		$data['agent'] = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
		if (!isset($data['path'])) {
			$data['path'] = implode('/', \App::get('router'));
		}
		$data['ip'] = request()->getIp();
		return $this->insert($data);
	}
}