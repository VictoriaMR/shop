<?php

namespace app\model\email;
use app\model\Base;

class Email extends Base
{
	protected $_table = 'email';
	protected $_primaryKey = 'email_id';

	const STATUS_WAIT = 0;
	const STATU_SENT_SUCCESS = 1;
	const STATU_SENT_FAILED = 2;

	const TYPE_LOGIN_SEND_CODE = 1;
	const TYPE_PASSWORD_RESET = 2;
}