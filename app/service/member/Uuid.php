<?php 

namespace app\service\member;
use app\service\Base;

class Uuid extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/member/Uuid');
	}

	public function getInfo($uuid)
	{
		$where = [
			'uuid' => $uuid,
			'site_id' => $this->siteId(),
		];
		return $this->loadData($where);
	}
}