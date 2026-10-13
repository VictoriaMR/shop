<?php

namespace app\model\log;
use app\model\Base;

class Payment extends Base
{
	protected $_connect = 'static';
	protected $_table = 'log_payment';
	protected $_primaryKey = 'log_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['log_id', 'mem_id', 'item_id', 'type'];

	public function addLog(array $data = [])
	{
		return $this->insertData($data);
	}
}
