<?php

namespace app\model;
use app\model\Base;

class Attribute extends Base
{
	protected $_table = 'attribute';
	protected $_primaryKey = 'attr_id';

	public function create(array $data) 
	{
		return $this->insertGetId($data);
	}
}