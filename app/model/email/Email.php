<?php

namespace app\model\email;
use app\model\Base;

class Email extends Base
{
	protected $_table = 'email';
	protected $_primaryKey = 'email_id';

	const TYPE_LOGIN_SEND_CODE = 1;
}