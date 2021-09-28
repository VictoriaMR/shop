<?php 

namespace app\service\function;
use app\service\Base;

class Icon extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/function/Icon');
	}
}