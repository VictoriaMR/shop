<?php

namespace app\model\product;
use app\model\Base;

class IntroduceUsed extends Base
{
	protected $_table = 'product_introduce_used';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'spu_id', 'attach_id', 'sort'];

	public function getInfoBySpuId($spuId)
	{
		$info = $this->where(['spu_id'=>(int)$spuId])->field('attach_id')->orderBy('sort', 'asc')->get();
		if (empty($info)) {
			return [];
		}
		return array_column($info, 'attach_id');
	}
}