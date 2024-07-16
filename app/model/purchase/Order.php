<?php

namespace app\model\purchase;
use app\model\Base;

class Order extends Base
{
	protected $_table = 'purchase_order';
	protected $_addTime = 'add_time';
	protected $_intFields = ['purchase_order_id', 'order_product_id', 'sku_id', 'purchase_sku_id', 'quantity', 'status', 'purchase_trade_id'];
}