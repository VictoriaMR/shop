<?php

namespace app\model\log;
use app\model\Base;

class Login extends Base
{
	protected $_connect = 'static';
	protected $_table = 'log_login';
	protected $_primaryKey = 'log_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['log_id', 'site_id', 'mem_id'];
}