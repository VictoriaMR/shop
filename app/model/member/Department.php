<?php

namespace app\model\member;
use app\model\Base;

class Department extends Base
{
	protected $_table = 'department';
	protected $_primaryKey = 'dept_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['dept_id', 'parent_id', 'status', 'sort'];
}
