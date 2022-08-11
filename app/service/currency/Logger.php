<?php 

namespace app\service\currency;
use app\service\Base;

class Logger extends Base
{	
	protected function getModel()
	{
		$this->baseModel = make('app/model/currency/Logger');
	}
}