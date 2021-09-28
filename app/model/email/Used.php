<?php

namespace app\model\email;
use app\model\Base;

class Used extends Base
{
	protected $_table = 'email_account_used';
	protected $_primaryKey = 'item_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['item_id', 'site_id', 'account_id'];
}