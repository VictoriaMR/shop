<?php

namespace app\model\product;
use app\model\Base;

class Language extends Base
{
	protected $_table = 'product_language';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'spu_id', ''];

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