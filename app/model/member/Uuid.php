<?php

namespace app\model\member;
use app\model\Base;

class Uuid extends Base
{
	protected $_table = 'member_uuid';
	protected $_primaryKey = 'uuid';
	protected $_addTime = 'add_time';
}