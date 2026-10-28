<?php

namespace app\service\member;
use app\service\Base;

class Address extends Base
{
	public function getInfo($id)
	{
		$where = [
			'address_id' => (int)$id,
			'mem_id'     => $this->userId(),
		];
		$info = $this->loadData($where);
		if (empty($info)) {
			return [];
		}
		$info['country'] = service('system/Country')->getName($info['country_code2'] ?? '');
		return $info;
	}
}
