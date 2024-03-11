<?php

namespace app\model\product;
use app\model\Base;

class SpuData extends Base
{
	protected $_table = 'product_spu_data';
	protected $_primaryKey = 'spu_id';
	protected $_intFields = ['spu_id', 'check_result'];
}