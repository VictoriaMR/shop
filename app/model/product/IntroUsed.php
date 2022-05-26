<?php

namespace app\model\product;
use app\model\Base;

class IntroUsed extends Base
{
	protected $_table = 'product_intro_used';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'spu_id', 'attach_id', 'sort'];

	public function getInfoBySpuId(int $spuId)
	{
		$info = $this->where(['spu_id'=>$spuId])->field('attach_id')->orderBy('sort', 'asc')->get();
		if (empty($info)) {
			return [];
		}
		return array_column($info, 'attach_id');
	}
}