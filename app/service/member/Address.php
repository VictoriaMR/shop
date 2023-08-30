<?php 

namespace app\service\member;
use app\service\Base;

class Address extends Base
{
	public function getInfo($id)
	{
		$where = [
			'address_id' => $id,
			'mem_id' => $this->userId(),
		];
		$info = $this->loadData($where);
		if (empty($info)) {
			return [];
		}
		//获取国家名称
		$info['country'] = make('app/service/address/Country')->getName($info['country_code2']);
		if (!empty($info['zone_id'])) {
			$info['state'] = make('app/service/address/Zone')->getName($info['zon_id']);
		}
		return $info;
	}
}