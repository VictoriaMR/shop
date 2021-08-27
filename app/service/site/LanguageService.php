<?php 

namespace app\service\site;
use app\service\Base;

class LanguageService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/site/Language');
	}
}