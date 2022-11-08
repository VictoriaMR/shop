<?php 

namespace app\service\address;
use app\service\Base;

class Zone extends Base
{
	protected $_model = 'app/model/address/Zone';

	public function getName($id, $lanId=2)
	{
		return $this->loadData($id, 'name_en')['name_en'] ?? '';
		if ($lanId == 2) {
			return $nameEn;
		}
		$temp = make('app/service/address/ZoneLanguage')->loadData(['zone_id'=>$id, 'lan_id'=>$lanId], 'name');
		if (empty($temp['name'])) {
			return $nameEn;
		}
		return $temp['name'];
	}
}