<?php

namespace app\model\log;

use app\model\Base;

class Attribute extends Base
{
	protected $_connect = 'static';
	protected $_table = 'log_attribute';
	protected $_primaryKey = 'log_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['log_id', 'mem_id', 'type', 'target_id'];

	public function addLog(array $data = [])
	{
		return $this->insertData($data);
	}
}
