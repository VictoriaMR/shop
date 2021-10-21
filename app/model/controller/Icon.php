<?php

namespace app\model\controller;
use app\model\Base;

class Icon extends Base
{
	protected $_table = 'controller_icon';
	protected $_primaryKey = 'icon_id';
	protected $_intFields = ['icon_id', 'sort'];
}