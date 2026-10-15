<?php

namespace app\model\controller;
use app\model\Base;

class Permission extends Base
{
	const TYPE_DEPARTMENT = 1; // 按部门授权
	const TYPE_MEMBER     = 2; // 按个人授权

	const SUPER_ADMIN_ID  = 10000; // 默认超级管理员ID

	protected $_table = 'controller_permission';
	protected $_primaryKey = 'perm_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['perm_id', 'target_type', 'target_id', 'add_time'];
}
