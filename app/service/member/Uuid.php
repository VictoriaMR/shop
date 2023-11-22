<?php 

namespace app\service\member;
use app\service\Base;

class Uuid extends Base
{
	public function getInfo($uuid)
	{
		$where = [
			'uuid' => $uuid,
		];
		return $this->loadData($where);
	}
}