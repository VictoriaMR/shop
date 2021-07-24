<?php 

namespace app\service\product;
use app\service\Base;

class SpuDataService extends Base
{
	public function getModel()
	{
		$this->baseModel = make('app/model/product/SpuData');
	}
}