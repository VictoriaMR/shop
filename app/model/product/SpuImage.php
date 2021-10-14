<?php

namespace app\model\product;
use app\model\Base;

class SpuImage extends Base
{
	protected $_table = 'product_spu_image';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'spu_id', 'attach_id', 'sort'];
}