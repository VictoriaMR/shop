<?php

namespace app\model\address;
use app\model\Base;

class Zone extends Base
{
	protected $_table = 'zone';
	protected $_primaryKey = 'zone_id';
	protected $_addTime = ['zone_id', 'sort'];
}