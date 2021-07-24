<?php

namespace app\model\product;
use app\model\Base;

class Sku extends Base
{
	protected $_table = 'product_sku';
	protected $_primaryKey = 'sku_id';
}