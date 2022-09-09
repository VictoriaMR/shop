<?php 

namespace app\service\product;
use app\service\Base;

class IntroUsed extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/IntroUsed');
	}

	public function addIntroUsed($spuId, array $data)
	{
		$allAttachId = array_column($data, 'attach_id');
		$list = $this->getListData(['spu_id'=>$spuId, 'attach_id'=>['in', $allAttachId]], 'attach_id');
		if (!empty($list)) {
			$list = array_column($list, 'attach_id');
			$list = array_diff($allAttachId, $list);
			if (empty($list)) {
				return true;
			}
			$data = array_column($data, null, 'attach_id');
			$tempData = [];
			foreach ($list as $value) {
				$tempData[] = $data[$value];
			}
			$data = $tempData;
		}
		return $this->insert($data);
	}

	public function getListById($spuId)
	{
		return $this->getListData(['spu_id'=>$spuId], 'attach_id', 0, 0, ['sort'=>'asc']);
	}
}