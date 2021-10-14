<?php

namespace app\model\message;
use app\model\Base;

class Message extends Base
{
	protected $_table = 'message';
	protected $_primaryKey = 'message_id';

	const SYSTEM_CONTACT_USER = 50001;
}