<?php

namespace app\model\login;
use app\model\Logger as Base;

class Logger extends Base
{
	protected $_connect = 'static';
	protected $_table = 'login_logger';
	protected $_primaryKey = 'log_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['log_id', 'site_id', 'mem_id', 'type', 'is_moblie'];

	const TYPE_LOGIN = 0;
	const TYPE_LOGOUT = 1;
	const TYPE_LOGIN_FAIL = 2;
	const TYPE_BEHAVIOR = 3;
}