<?php 

namespace app\service\system;

class System
{
	public function __call($func, $arg)
	{
		return service('system/'.ucfirst($func));
	}
}