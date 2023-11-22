<?php

namespace app\model\controller;
use app\model\Base;

class Controller extends Base
{
	protected $_table = 'controller';
	protected $_primaryKey = 'con_id';
	protected $_withSiteId = false;
	protected $_intFields = ['con_id', 'sort'];
}