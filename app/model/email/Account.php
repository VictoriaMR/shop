<?php

namespace app\model\email;
use app\model\Base;

class Account extends Base
{
	protected $_table = 'email_account';
	protected $_primaryKey = 'account_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['account_id', 'smtp_ssl', 'status'];
}