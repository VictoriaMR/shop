<?php

namespace app\model;
use app\model\Base;

class Logger extends Base
{
	protected $_connect = 'static';
	protected $_table = 'visitor_log';
	protected $_primaryKey = 'log_id';
}