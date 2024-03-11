<?php

namespace app\model\order;
use app\model\Base;

class StatusHistory extends Base
{
	protected $_table = 'order_status_history';
	protected $_primaryKey = 'item_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['item_id', 'order_id', 'status'];
}