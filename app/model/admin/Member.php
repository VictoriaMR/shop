<?php

namespace app\model\admin;
use app\model\Base;

class Member extends Base
{
	protected $_table = 'admin_member';
	protected $_primaryKey = 'mem_id';

	public function addLog(array $data)
	{
		return make('app/model/admin/Logger')->addLog($data);
	}
}