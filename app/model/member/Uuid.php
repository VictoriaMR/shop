<?php

namespace app\model\member;
use app\model\Base;

class Uuid extends Base
{
	protected $_table = 'member_uuid';
	protected $_primaryKey = 'item_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['item_id', 'site_id', 'mem_id'];
}