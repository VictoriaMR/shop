<?php

namespace app\model\currency;
use app\model\Base;

class Currency extends Base
{
	protected $_table = 'currency';
	protected $_primaryKey = 'code';
	protected $_updateTime = 'update_time';
}