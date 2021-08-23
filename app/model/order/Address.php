<?php

namespace app\model\order;
use app\model\Base;

class Address extends Base
{
	protected $_table = 'order_address';
	protected $_primaryKey = 'order_address_id';
}