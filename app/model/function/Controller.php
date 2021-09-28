<?php

namespace app\model\function;
use app\model\Base;

class Controller extends Base
{
	protected $_table = 'function_controller';
	protected $_primaryKey = 'con_id';
	protected $_intFields = ['con_id', 'sort'];
}