<?php

namespace app\model;
use app\model\Base;

class Member extends Base
{
	protected $_table = 'member';
	protected $_primaryKey = 'mem_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['mem_id', 'site_id', 'sex', 'status', 'verify'];

	const INFO_CACHE_TIMEOUT = 3600 * 24;
	const TOKEN_CACHE_TIMEOUT = 3600 * 24 *7;

	public function addLog(array $data=[])
	{
		return make('app/model/Logger')->addLog($data);
	}
}