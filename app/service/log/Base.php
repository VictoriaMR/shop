<?php 

namespace app\service\log;

class Base
{
	public function __call($method, $arg)
	{
		return service('log/'.ucfirst($method));
	}
}