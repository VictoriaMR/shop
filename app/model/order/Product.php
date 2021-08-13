<?php

namespace app\model\order;
use app\model\Base;

class Product extends Base
{
	protected $_table = 'order_product';
	protected $_primaryKey = 'order_product_id';
}