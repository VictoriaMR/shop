<?php

namespace app\model\product;
use app\model\Base;

class Introduce extends Base
{
	protected $_table = 'product_introduce';

	public function getInfoBySpuId($spuId)
	{
		$info = $this->where(['spu_id'=>(int)$spuId])->field('attach_id')->orderBy('sort', 'asc')->get();
		if (empty($info)) {
			return [];
		}
		return array_column($info, 'attach_id');
	}
}