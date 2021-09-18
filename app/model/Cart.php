<?php

namespace app\model;
use app\model\Base;

class Cart extends Base
{
	const CART_CHECKED = 1;

	protected $_table = 'cart';
	protected $_primaryKey = 'cart_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
}