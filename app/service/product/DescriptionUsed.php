<?php 

namespace app\service\product;
use app\service\Base;

class DescriptionUsed extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/DescriptionUsed');
	}

	public function addDescUsed($spuId, array $insert)
	{
		if (empty($insert)) return false;
		//获取已有的列表
		$list = $this->getListData(['spu_id'=>$spuId], 'name_id,value_id');
		if (!empty($list)) {
			$tempData = [];
			foreach ($list as $value) {
				$tempData[$value['name_id'].'-'.$value['value_id']] = true;
			}
			if (!empty($tempData)) {
			if (empty($insert[0])) $insert = [$insert];
				foreach ($insert as $key => $value) {
					if (isset($tempData[$value['name_id'].'-'.$value['value_id']])) {
						unset($insert[$key]);
					}
				}
			}
		}
		return $this->insert($insert);
	}
}