<?php 

namespace app\service\product;
use app\service\Base;

class IntroduceUsed extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/product/IntroduceUsed');
	}

	public function addIntroduceUsed($spuId, array $data)
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
		$attachArr = $this->getListData(['spu_id'=>$spuId], 'attach_id', 0, 0, ['sort'=>'asc']);
		$list = make('app/service/attachment/Attachment')->getList(['attach_id'=>['in', array_column($attachArr, 'attach_id')]]);
		$list = array_column($list, null, 'attach_id');
		foreach ($attachArr as $key => $value) {
			$attachArr[$key] = $list[$value['attach_id']];
		}
		return $attachArr;
	}
}