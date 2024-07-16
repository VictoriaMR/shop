<?php

namespace app\model\purchase;
use app\model\Base;

class Order extends Base
{
	protected $_table = 'purchase_trade_detail';
	protected $_intFields = ['item_id', 'purchase_trade_id', 'type'];
}