<?php

namespace app\model;
use app\model\Base;

class Cart extends Base
{
	const CART_CHECKED = 1;

	protected $_table = 'cart';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['cart_id', 'site_id', 'mem_id', 'sku_id', 'quantity', 'checked'];
}