<?php 

namespace app\service\product;
use app\service\Base;

class Language extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/Language');
	}

	public function setNxLanguage($spuId, $lanId, $name)
	{
		if (empty($spuId) || empty($lanId) || empty($name)) {
			return false;
		}
		$where = ['spu_id'=>$spuId, 'lan_id'=>$lanId];
		if ($this->getCountData($where)) {
			return $this->updateData($where, ['name' => $name]);
		} else {
			$where['name'] = $name;
			return $this->insert($where);
		}
	}
}