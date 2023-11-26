<?php

namespace app\model;
use app\model\Base;

class Logger extends Base
{
	protected $_connect = 'static';
	protected $_table = 'visitor_logger';
	protected $_primaryKey = 'log_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['log_id', 'site_id', 'mem_id', 'is_moblie'];

	public function addLog(array $data)
	{
		$data['site_id'] = siteId();
		$data['mem_id'] = userId();
		$data['lan_id'] = lanId();
		$data['is_moblie'] = isMobile() ? 1 : 0;
		$data['browser'] = request()->getBrowser();
		$data['system'] = request()->getSystem();
		$data['agent'] = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
		$data['ip'] = request()->getIp();
		return $this->insert($data);
	}
}