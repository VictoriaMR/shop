<?php

namespace app\model\payment;
use app\model\Base;

class Payment extends Base
{
	protected $_table = 'payment';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['payment_id', 'type', 'status', 'is_sandbox'];
}