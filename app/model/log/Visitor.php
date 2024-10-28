<?php

namespace app\model\log;
use app\model\Base;

class Visitor extends Base
{
	protected $_connect = 'static';
	protected $_table = 'log_visitor';
	protected $_primaryKey = 'log_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['log_id', 'site_id', 'mem_id', 'is_moblie'];

	public function addLog(array $data=[])
	{
		$data['site_id'] = siteId();
		$data['mem_id'] = userId();
		$data['lan_id'] = lanId();
		$data['is_moblie'] = isMobile() ? 1 : 0;
		$data['browser'] = frame('Request')->getBrowser();
		$data['system'] = frame('Request')->getSystem();
		$data['agent'] = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
		$data['ip'] = frame('IP')->getIp();
		return $this->insert($data);
	}

	public function getStats()
	{

	}

	public function getIpDateStat()
	{
		
	}
}