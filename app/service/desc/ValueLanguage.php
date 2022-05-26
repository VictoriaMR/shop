<?php 

namespace app\service\desc;
use app\service\Base;

class ValueLanguage extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/desc/ValueLanguage');
	}
}