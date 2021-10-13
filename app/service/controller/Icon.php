<?php 

namespace app\service\controller;
use app\service\Base;

class Icon extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/controller/Icon');
	}
}