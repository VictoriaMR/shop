<?php 

namespace app\service\address;
use app\service\Base;

class Zone extends Base
{
	public function getName($id, $lanId=2)
	{
		return $this->loadData($id, 'name_en')['name_en'] ?? '';
		if ($lanId == 2) {
			return $nameEn;
		}
		$temp = service('address/ZoneLanguage')->loadData(['zone_id'=>$id, 'lan_id'=>$lanId], 'name');
		if (empty($temp['name'])) {
			return $nameEn;
		}
		return $temp['name'];
	}
}