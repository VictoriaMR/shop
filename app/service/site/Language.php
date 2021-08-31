<?php 

namespace app\service\site;
use app\service\Base;

class Language extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/site/Language');
	}
}