<?php

namespace app\model;

use app\model\Base;

class ProductSpuImage extends Base
{
	//表名
	protected $_table = 'product_spu_image';

	public function getInfoBySpuId($spuId)
	{
		$info = $this->where(['spu_id'=>(int)$spuId])->field('attach_id')->get();
		if (empty($info)) {
			return [];
		}
		return array_column($info, 'attach_id');
	}
}