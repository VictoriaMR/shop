<?php

namespace app\service\system;
use app\service\Base;

class Country extends Base
{
	public function getList()
	{
		return $this->getListData(['status' => 1], 'code2,dialing_code,name_en as name', 0, 0, ['sort' => 'asc', 'name_en' => 'asc']);
	}

	public function getName($code2)
	{
		if (empty($code2)) {
			return '';
		}
		$row = $this->loadData(['code2' => strtoupper(trim((string)$code2))], 'name_en');
		return $row['name_en'] ?? (string)$code2;
	}
}
