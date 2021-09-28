<?php

namespace app\model\member;
use app\model\Base;

class Collect extends Base
{
	protected $_table = 'member_collect';
	protected $_primaryKey = 'collect_id';
	protected $_addTime = 'add_time';
	protected $_intFields = ['coll_id', 'mem_id', 'spu_id'];
}