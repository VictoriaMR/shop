<?php 

namespace app\service\address;
use app\service\Base;

class CountryService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/address/Country');
	}

	public function getName($code2)
	{
		return $this->loadData($code2, 'name_en')['name_en'] ?? '';
	}
}