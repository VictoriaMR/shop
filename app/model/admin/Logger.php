<?php

namespace app\model\admin;
use app\model\Base;

class Logger extends Base
{
	protected $_connect = 'static';
	protected $_table = 'admin_logger';
	protected $_primaryKey = 'log_id';
	const TYPE_LOGIN = 0;
	const TYPE_LOGOUT = 1;
}