<?php

namespace app\model\product;
use app\model\Base;

class SkuAttr extends Base
{
	protected $_table = 'product_sku_attr';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'sku_id', 'attach_id'];
}
