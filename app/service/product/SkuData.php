<?php 

namespace app\service\product;
use app\service\Base;

class SkuData extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/SkuData');
	}
}