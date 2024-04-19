<?php

namespace app\model\purchase;
use app\model\Base;

class ProductItem extends Base
{
	protected $_table = 'purchase_product_item';
	protected $_intFields = ['purchase_product_item_id', 'purchase_product_id', 'stock'];
}