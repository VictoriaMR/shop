<?php 

namespace app\service\product;
use app\service\Base;

class Language extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/Language');
	}
}