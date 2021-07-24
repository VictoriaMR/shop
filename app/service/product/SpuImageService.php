<?php 

namespace app\service\product;
use app\service\Base;

class SpuImageService extends Base
{
	protected function getModel()
	{
		$this->baseModel= make('app/model/product/SpuImage');
	}

	public function getListBySpuId($spuId)
	{
		$attachArr = $this->getListData(['spu_id'=>$spuId], 'attach_id');
		return make('app/service/AttachmentService')->getList(['attach_id'=>['in', array_unique(array_column($attachArr, 'attach_id'))]]);
	}
}