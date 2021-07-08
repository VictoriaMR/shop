<?php

namespace app\model;
use app\model\Base;

class Category extends Base
{
	protected $_table = 'category';
	protected $_primaryKey = 'cate_id';

	public function getInfo($fields)
	{
		return $this->loadData(null, $fields);
	}

	public function create(array $data) 
	{
		return $this->insertGetId($data);
	}
}