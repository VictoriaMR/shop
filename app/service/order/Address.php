<?php 

namespace app\service\order;
use app\service\Base;

class Address extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/order/Address');
	}
}