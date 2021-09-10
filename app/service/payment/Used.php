<?php 

namespace app\service\payment;
use app\service\Base;

class Used extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/payment/Used');
	}
}