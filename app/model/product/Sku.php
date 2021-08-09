<?php

namespace app\model\product;
use app\model\Base;

class Sku extends Base
{
	protected $_table = 'product_sku';
	protected $_primaryKey = 'sku_id';

	const STATUS_CLOSE = 0;
	const STATUS_OPEN = 1;
	const CACHE_INFO_KEY = 'sku-info:';
	const CACHE_EXPIRE_TIME = 3600*24;
}