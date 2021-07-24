<?php

namespace app\model\product;
use app\model\Base;

class AttributeRelation extends Base
{
	protected $_table = 'product_attribute_relation';

	public function getInfo($fields)
	{
		return $this->loadData(null, $fields);
	}

	public function create(array $data) 
	{
		return $this->insert($data);
	}
}