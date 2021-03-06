<?php

namespace app\model\payment;
use app\model\Base;

class Used extends Base
{
	protected $_table = 'payment_used';
	protected $_primaryKey = 'item_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['item_id', 'site_id', 'type', 'payment_id', 'sort'];
}