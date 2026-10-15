<?php

namespace app\model\log;
use app\model\Base;

class Member extends Base
{
	const TYPE_MEMBER     = 0; // 人员/登录日志
	const TYPE_DEPARTMENT = 1; // 部门管理日志
	const TYPE_POSITION   = 2; // 职位管理日志

	protected $_connect = 'static';
	protected $_table = 'log_member';
	protected $_primaryKey = 'log_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['log_id', 'site_id', 'mem_id', 'type'];
}
