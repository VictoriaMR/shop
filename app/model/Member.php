<?php

namespace app\model;
use app\model\Base;

class Member extends Base
{
	protected $_table = 'member';
	protected $_primaryKey = 'mem_id';
	const INFO_CACHE_TIMEOUT = 3600 *24;
}