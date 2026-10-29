<?php

namespace app\service\log;
use app\service\Base;

class System extends Base
{
	public function add($info)
	{
		if (empty($info)) return false;
		return $this->insertData([
			'mem_id' => userId(),
			'site_id' => siteId(),
			'ip' => frame('IP')->getIp(),
			'info' => trim((string)$info),
		]);
	}

	public function addLog($info)
	{
		return $this->add($info);
	}
}
