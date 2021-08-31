<?php 

namespace app\service\address;
use app\service\Base;

class ZoneLanguage extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/address/ZoneLanguage');
	}
}