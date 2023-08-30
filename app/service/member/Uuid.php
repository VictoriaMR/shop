<?php 

namespace app\service\member;
use app\service\Base;

class Uuid extends Base
{
	public function getInfo($uuid)
	{
		$where = [
			'uuid' => $uuid,
			'site_id' => $this->siteId(),
		];
		return $this->loadData($where);
	}
}