<?php

namespace app\model\product;
use app\model\Base;

class AttrUsed extends Base
{
	protected $_table = 'product_attr_used';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'sku_id', 'attrn_id', 'attrv_id', 'attach_id', 'sort'];
}