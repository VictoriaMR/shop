<?php

namespace app\model\address;
use app\model\Base;

class Zone extends Base
{
	protected $_table = 'zone';
	protected $_intFields = ['zone_id', 'sort'];
}