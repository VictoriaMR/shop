<?php

namespace app\model\desc;
use app\model\Base;

class Name extends Base
{
	protected $_table = 'desc_name';
	protected $_primaryKey = 'descn_id';
	protected $_intFields = ['descn_id', 'status'];
}