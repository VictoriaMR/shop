<?php 

namespace app\service\order;
use app\service\Base;

class Product extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/order/Product');
	}
}