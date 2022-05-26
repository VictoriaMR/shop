<?php

namespace app\model\product;
use app\model\Base;

class DescUsed extends Base
{
	protected $_table = 'product_desc_used';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'spu_id', 'descn_id', 'descv_id', 'sort'];
}