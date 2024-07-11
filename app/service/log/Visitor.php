<?php 

namespace app\service\log;
use app\service\Base;

class Visitor extends Base 
{
	public function add($data)
	{
		$data['site_id'] = siteId();
		$data['mem_id'] = userId();
		$data['lan_id'] = lanId();
		$data['is_moblie'] = isMobile() ? 1 : 0;
		$data['browser'] = request()->getBrowser();
		$data['system'] = request()->getSystem();
		$data['agent'] = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
		$data['ip'] = request()->getIp();
		return $this->insertData($data);
	}
}