<?php

namespace app\model\attr;
use app\model\Base;

class Value extends Base
{
	protected $_table = 'attrvalue';
	protected $_primaryKey = 'attv_id';
	protected $_intFields = ['attv_id', 'status'];
}