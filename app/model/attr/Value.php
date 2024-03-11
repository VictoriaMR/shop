<?php

namespace app\model\attr;
use app\model\Base;

class Value extends Base
{
	protected $_table = 'attr_value';
	protected $_primaryKey = 'attrv_id';
	protected $_intFields = ['attrv_id', 'status'];
}