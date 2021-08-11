<?php 

namespace app\service\address;
use app\service\Base;

class ZoneService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/address/Zone');
	}
}