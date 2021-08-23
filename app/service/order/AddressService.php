<?php 

namespace app\service\order;
use app\service\Base;

class AddressService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/order/Address');
	}
}