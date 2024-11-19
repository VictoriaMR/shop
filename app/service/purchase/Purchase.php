<?php 

namespace app\service\purchase;

class Purchase
{
	public function __call($func, $arg)
	{
		return service('purchase/'.ucfirst($func));
	}
}