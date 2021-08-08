<?php 

namespace app\service\product;
use app\service\Base;

class SpuImageService extends Base
{
	protected function getModel()
	{
		$this->baseModel= make('app/model/product/SpuImage');
	}

	public function addSpuImage(array $data)
	{
		if (!empty($data[0]) && is_array($data[0])) {
			foreach ($data as $key => $value) {
				if ($this->getCountData($value)) {
					unset($data[$key]);
				}
			}
		}
		return $this->insert($data);
	}

	public function getListById($spuId, $attactId=[])
	{
		$attachArr = $this->getListData(['spu_id'=>$spuId], 'attach_id', 0, 0, ['sort'=>'asc']);
		$list = make('app/service/AttachmentService')->getList(['attach_id'=>['in', array_column($attachArr, 'attach_id')]]);
		$list = array_column($list, null, 'attach_id');
		foreach ($attachArr as $key => $value) {
			$attachArr[$key] = $list[$value['attach_id']];
		}
		return $attachArr;
	}
}