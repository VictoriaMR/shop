<?php

namespace app\model\product;
use app\model\Base;

class SkuData extends Base
{
	protected $_table = 'product_sku_data';
	protected $_primaryKey = 'sku_id';
	protected $_intFields = ['sku_id', 'item_id'];
}