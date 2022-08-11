<?php

namespace app\model\currency;
use app\model\Base;

class Logger extends Base
{
	protected $_table = 'currency_log';
	protected $_primaryKey = 'log_id';
	protected $_addTime = 'add_time';
}