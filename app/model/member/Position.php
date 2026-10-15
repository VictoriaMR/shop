<?php

namespace app\model\member;
use app\model\Base;

class Position extends Base
{
	protected $_table = 'position';
	protected $_primaryKey = 'pos_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['pos_id', 'status', 'sort'];
}
