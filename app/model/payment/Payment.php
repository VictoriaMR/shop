<?php

namespace app\model\payment;
use app\model\Base;

class Payment extends Base
{
	protected $_table = 'payment';
	protected $_primaryKey = 'payment_id';
	protected $_addTime = 'add_time';
}