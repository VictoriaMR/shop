<?php

namespace app\model\system;
use app\model\Base;

class Currency extends Base
{
	protected $_table = 'sys_currency';
	protected $_primaryKey = 'code';
	protected $_updateTime = 'update_time';
}