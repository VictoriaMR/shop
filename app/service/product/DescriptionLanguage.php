<?php 

namespace app\service\product;
use app\service\Base;

class DescriptionLanguage extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/DescriptionLanguage');
	}
}