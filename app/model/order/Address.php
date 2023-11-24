<?php

namespace app\model\order;
use app\model\Base;

class Address extends Base
{
	protected $_table = 'order_address';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['order_address_id', 'order_id', 'type', 'zone_id'];
}