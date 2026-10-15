<?php

namespace app\service\product;
use app\service\Base;

class Language extends Base
{
	public function setNxLanguage($spuId, $lanId, $name)
	{
		$spuId = (int)$spuId;
		if ($spuId <= 0 || $lanId === '' || $lanId === null || $name === '') {
			return false;
		}
		$where = ['spu_id' => $spuId, 'lan_id' => (int)$lanId];
		if ($this->getCountData($where)) {
			return $this->updateData($where, ['name' => $name]);
		}
		$where['name'] = $name;
		return $this->insert($where);
	}
}