<?php

namespace app\model\order;
use app\model\Base;

class ProductAttribute extends Base
{
	protected $_table = 'order_product_attribute';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'order_product_id', 'attr_id', 'attv_id', 'attach_id'];
}