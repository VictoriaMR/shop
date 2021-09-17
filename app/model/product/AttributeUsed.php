<?php

namespace app\model\product;
use app\model\Base;

class AttributeUsed extends Base
{
	protected $_table = 'product_attribute_used';

	public function getInfo($fields)
	{
		return $this->loadData(null, $fields);
	}

	public function create(array $data) 
	{
		return $this->insert($data);
	}
}