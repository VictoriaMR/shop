<?php 

namespace app\service\product;
use app\service\Base;

class DescUsed extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/DescUsed');
	}

	public function addDescUsed(int $spuId, array $insert)
	{
		if (empty($insert)) return false;
		//获取已有的列表
		$list = $this->getListData(['spu_id'=>$spuId], 'descn_id,descv_id');
		if (!empty($list)) {
			$tempData = [];
			foreach ($list as $value) {
				$tempData[$value['descn_id'].'-'.$value['descv_id']] = true;
			}
			if (!is_array(current($insert))) $insert = [$insert];
			foreach ($insert as $key => $value) {
				if (isset($tempData[$value['descn_id'].'-'.$value['descv_id']])) {
					unset($insert[$key]);
				}
			}
		}
		return $this->insert($insert);
	}

	public function getListById($spuId)
	{
		$list = $this->getListData(['spu_id'=>$spuId], 'descn_id,descv_id', 0, 0, ['sort'=>'asc']);
		if (empty($list)) {
			return false;
		}
		$nameArr = array_unique(array_column($list, 'descn_id'));
		$valueArr = array_unique(array_column($list, 'descv_id'));
		$nameArr = make('app/service/desc/Name')->getListById($nameArr);
		$valueArr = make('app/service/desc/Value')->getListById($valueArr);

		$nameArr = array_column($nameArr, 'name', 'descn_id');
		$valueArr = array_column($valueArr, 'name', 'descv_id');
		foreach ($list as $key => $value) {
			$list[$key]['name'] = $nameArr[$value['descn_id']] ?? '';
			$list[$key]['value'] = $valueArr[$value['descv_id']] ?? '';
		}
		return $list;
	}
}