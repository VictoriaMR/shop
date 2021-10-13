<?php

namespace app\model\product;
use app\model\Base;

class DescriptionUsed extends Base
{
	protected $_table = 'product_description_used';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'spu_id', 'desc_id', 'sort'];
}