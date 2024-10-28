<?php

namespace app\model\system;
use app\model\Base;

class Country extends Base
{
	protected $_table = 'sys_country';
	protected $_primaryKey = 'code2';
	protected $_intFields = ['dialing_code', 'sort', 'status'];
}