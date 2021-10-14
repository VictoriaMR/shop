<?php

namespace app\model\supplier;
use app\model\Base;

class Url extends Base
{
	protected $_table = 'supplier_url';
	protected $_primaryKey = 'supp_id';
	protected $_addTime = 'add_time';
}