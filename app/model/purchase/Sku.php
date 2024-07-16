<?php

namespace app\model\purchase;
use app\model\Base;

class Sku extends Base
{
	protected $_table = 'purchase_sku';
	protected $_intFields = ['purchase_sku_id', 'purchase_spu_id', 'stock'];
}