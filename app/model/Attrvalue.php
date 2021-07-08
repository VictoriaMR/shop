<?php

namespace app\model;
use app\model\Base;

class Attrvalue extends Base
{
	protected $_table = 'attrvalue';
	protected $_primaryKey = 'attv_id';

	public function getInfo($fields)
	{
		return $this->loadData(null, $fields);
	}

	public function create(array $data) 
	{
		return $this->insertGetId($data);
	}
}