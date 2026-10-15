<?php

namespace app\model\member;
use app\model\Base;

class MemberDepartment extends Base
{
	protected $_table = 'member_department';
	protected $_primaryKey = 'item_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['item_id', 'mem_id', 'dept_id', 'pos_id'];
}
