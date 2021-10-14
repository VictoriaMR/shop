<?php

namespace app\model\member;
use app\model\Base;

class History extends Base
{
	protected $_table = 'member_history';
	protected $_primaryKey = 'his_id';
	protected $_addTime = 'add_date,add_time';
	protected $_intFields = ['his_id', 'mem_id', 'spu_id'];
}