<?php

namespace app\model\order;
use app\model\Base;

class Product extends Base
{
	protected $_table = 'order_product';
	protected $_intFields = ['order_product_id', 'order_id', 'sku_id', 'attach_id', 'quantity'];
}