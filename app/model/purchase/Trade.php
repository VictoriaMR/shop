<?php

namespace app\model\purchase;
use app\model\Base;

class Trade extends Base
{
	protected $_table = 'purchase_trade';
	protected $_addTime = 'add_time';
	protected $_intFields = ['purchase_trade_id', 'fin_account_id', 'status', 'express_id'];
}
