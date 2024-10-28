<?php 

namespace app\service\log;
use app\service\Base;

class Login extends Base
{
	public function addLog($info='')
	{
		return $this->insertData([
			'mem_id' => userId(),
			'site_id' => siteId(),
			'info' => trim($info),
			'ip' => frame('IP')->getIp(),
		]);
	}
}