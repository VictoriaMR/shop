<?php 

namespace app\service\attachment;
use app\service\Base;

class Url extends Base
{	
	protected function getModel()
	{
		$this->baseModel = make('app/model/attachment/Url');
	}
}