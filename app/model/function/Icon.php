<?php

namespace app\model\function;
use app\model\Base;

class Icon extends Base
{
	protected $_table = 'function_icon';
	protected $_primaryKey = 'icon_id';
	protected $_intFields = ['icon_id', 'sort'];
}