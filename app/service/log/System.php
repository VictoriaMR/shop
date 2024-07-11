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
			'ip' => request()->getIp(),
			'info' => trim($info),
		]);
	}
}