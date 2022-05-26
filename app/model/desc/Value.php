<?php

namespace app\model\desc;
use app\model\Base;

class Value extends Base
{
	protected $_table = 'desc_value';
	protected $_primaryKey = 'descv_id';
	protected $_intFields = ['descv_id', 'status'];
}