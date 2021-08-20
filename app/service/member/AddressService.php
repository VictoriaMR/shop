<?php 

namespace app\service\member;
use app\service\Base;

class AddressService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/member/Address');
	}

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
		$info['country'] = make('app/service/address/CountryService')->getName($info['country_code2'], $info['lan_id']);
		if (!empty($info['zone_id'])) {
			$info['state'] = make('app/service/address/ZoneService')->getName($info['zon_id'], $info['lan_id']);
		}
		return $info;
	}
}