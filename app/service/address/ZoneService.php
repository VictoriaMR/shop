<?php 

namespace app\service\address;
use app\service\Base;

class ZoneService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/address/Zone');
	}

	public function getName($id, $lanId=2)
	{
		$nameEn = $this->loadData($id, 'name_en')['name_en'] ?? '';
		if ($lanId == 2) {
			return $nameEn;
		}
		$temp = make('app/service/address/ZoneLanguageService')->loadData(['zone_id'=>$id, 'lan_id'=>$lanId], 'name');
		if (empty($temp['name'])) {
			return $nameEn;
		}
		return $temp['name'];
	}
}