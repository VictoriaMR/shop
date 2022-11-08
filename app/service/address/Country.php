<?php 

namespace app\service\address;
use app\service\Base;

class Country extends Base
{
	protected $_model = 'app/model/address/Country';

	public function getName($code2, $lanId=2)
	{
		return $this->loadData($code2, 'name_en')['name_en'] ?? '';
		if ($lanId == 2) {
			return $nameEn;
		}
		$temp = make('app/service/address/CountryLanguage')->loadData(['country_code2'=>$code2, 'lan_id'=>$lanId], 'name');
		if (empty($temp['name'])) {
			return $nameEn;
		}
		return $temp['name'];
	}
}