<?php

namespace app\service\system;
use app\service\Base;

class Zone extends Base
{
	public function getList($countryCode2 = '')
	{
		$where = [];
		if ($countryCode2 !== '') {
			$where['country_code2'] = strtoupper(trim((string)$countryCode2));
		}
		return $this->getListData($where, 'zone_id,country_code2,name_en as name', 0, 0, ['name_en' => 'asc']);
	}

	public function getName($zoneId)
	{
		$zoneId = (int)$zoneId;
		if ($zoneId <= 0) {
			return '';
		}
		$row = $this->loadData(['zone_id' => $zoneId], 'name_en');
		return $row['name_en'] ?? '';
	}
}
