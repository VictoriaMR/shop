<?php 

namespace app\service\address;
use app\service\Base;

class ZoneLanguageService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/address/ZoneLanguage');
	}
}