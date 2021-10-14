<?php

namespace app\model\supplier;
use app\model\Base;

class Shop extends Base
{
	protected $_table = 'supplier_shop';
	protected $_primaryKey = 'shop_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
}