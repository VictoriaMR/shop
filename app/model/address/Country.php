<?php

namespace app\model\address;
use app\model\Base;

class Country extends Base
{
	protected $_table = 'country';
	protected $_primaryKey = 'code2';
	protected $_intFields = ['dialing_code', 'sort', 'status'];
}