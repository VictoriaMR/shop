<?php

namespace app\model\admin;
use app\model\Base;

class Logger extends Base
{
	protected $_connect = 'static';
	protected $_table = 'admin_logger';
	protected $_primaryKey = 'log_id';
	
	const TYPE_LOGIN = 0;
	const TYPE_LOGOUT = 1;
	const TYPE_LOGIN_FAIL = 2;
	const TYPE_BEHAVIOR = 3;

	public function addLog(array $data=[])
	{
		$data['mem_id'] = userId();
		$data['browser'] = request()->getBrowser();
		$data['system'] = request()->getSystem();
		$data['agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
		$data['path'] = implode('/', \App::get('router'));
		$data['ip'] = request()->getIp();
		$data['add_time'] = now();
		return $this->insert($data);
	}
}