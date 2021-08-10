<?php 

namespace app\service;
use app\service\Base;

class CountryService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/Country');
	}
}