<?php

namespace app\model\order;
use app\model\Base;

class ProductAttribute extends Base
{
	protected $_table = 'order_product_attribute';
	protected $_primaryKey = 'item_id';
}