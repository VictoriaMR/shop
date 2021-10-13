<?php

namespace app\model\product;
use app\model\Base;

class AttributeUsed extends Base
{
	protected $_table = 'product_attribute_used';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'sku_id', 'attr_id', 'attv_id', 'attach_id', 'sort'];
}