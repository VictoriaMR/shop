<?php 

namespace app\service\member;
use app\service\Base;

class AddressService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/member/Address');
	}
}