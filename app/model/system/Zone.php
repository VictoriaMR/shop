<?php

namespace app\model\system;
use app\model\Base;

class Zone extends Base
{
	protected $_table = 'sys_zone';
	protected $_intFields = ['zone_id', 'sort'];
}