<?php

namespace app\model\log;
use app\model\Base;

class Order extends Base
{
	protected $_connect = 'static';
	protected $_table = 'log_order';
	protected $_primaryKey = 'log_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['log_id', 'mem_id', 'order_id', 'order_product_id'];
}