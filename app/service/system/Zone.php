<?php 

namespace app\service\system;
use app\service\Base;

class Zone extends Base
{
	public function getList()
	{
		return $this->getListData([], 'country_code2,name_en as name');
	}
}