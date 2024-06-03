<?php

namespace app\model\log;
use app\model\Base;

class Currency extends Base
{
	protected $_connect = 'static';
	protected $_table = 'log_currency';
	protected $_primaryKey = 'log_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['log_id'];

	public function addLog(array $data=[])
	{
		return $this->insertData($data);
	}
}