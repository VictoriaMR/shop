<?php 

namespace app\service\description;
use app\service\Base;

class Language extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/description/Language');
	}
}