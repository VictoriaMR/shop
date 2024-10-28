<?php 

namespace app\service\system;
use app\service\Base;

class Country extends Base
{
	public function getList()
	{
		return $this->getListData([], 'code2,dialing_code,name_en as name');
	}
}