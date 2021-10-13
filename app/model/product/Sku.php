<?php

namespace app\model\product;
use app\model\Base;

class Sku extends Base
{
	protected $_table = 'product_sku';
	protected $_primaryKey = 'sku_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['sku_id', 'spu_id', 'site_id', 'status', 'attach_id', 'stock', 'sale_total'];

	const STATUS_CLOSE = 0;
	const STATUS_OPEN = 1;
	const CACHE_INFO_KEY = 'sku-info:';
	const CACHE_EXPIRE_TIME = 3600*24;
}