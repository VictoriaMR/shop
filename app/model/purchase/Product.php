<?php

namespace app\model\purchase;
use app\model\Base;

class Product extends Base
{
	protected $_table = 'purchase_product';
	protected $_primaryKey = 'purchase_product_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['purchase_product_id', 'purchase_channel_id', 'user_id', 'status'];
}