<?php 

namespace app\service\attr;
use app\service\Base;

class ButeLanguage extends Base
{
	protected function getModel()
	{
		return $this->baseModel = make('app/model/attr/ButeLanguage');
	}
}