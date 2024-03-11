<?php 

namespace app\service\product;
use app\service\Base;

class SpuImage extends Base
{
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
		return $this->getListData(['spu_id'=>$spuId], 'attach_id', 0, 0, ['sort'=>'asc']);
	}
}