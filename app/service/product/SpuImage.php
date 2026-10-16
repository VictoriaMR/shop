<?php 

namespace app\service\product;
use app\service\Base;

class SpuImage extends Base
{
	public function addSpuImage(array $data)
	{
		if (empty($data)) {
			return false;
		}
		if (!empty($data[0]) && is_array($data[0])) {
			foreach ($data as $key => $value) {
				if ($this->getCountData(['spu_id' => $value['spu_id'], 'attach_id' => $value['attach_id']])) {
					unset($data[$key]);
				}
			}
			$data = array_values($data);
			if (empty($data)) {
				return true;
			}
		}
		return $this->insert($data);
	}

	public function getListById($spuId, $attactId=[])
	{
		return $this->getListData(['spu_id'=>$spuId], 'attach_id', 0, 0, ['sort'=>'asc']);
	}
}