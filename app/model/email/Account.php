<?php

namespace app\model\email;
use app\model\Base;

class Account extends Base
{
	protected $_table = 'email_account';
	protected $_primaryKey = 'account_id';
}