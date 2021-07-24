<?php

namespace app\model\product;
use app\model\Base;

class Language extends Base
{
	protected $_table = 'product_language';

	public function getInfo($fields)
	{
		return $this->loadData(null, $fields);
	}

	public function create(array $data) 
	{
		return $this->insert($data);
	}

	public function isExist($spuId, $lanId)
	{
		return $this->getCount(['spu_id' => (int)$spuId, 'lan_id' => (int)$lanId]) > 0;
	}
}