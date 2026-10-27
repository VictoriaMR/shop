<?php

namespace app\model\system;
use app\model\Base;

class Language extends Base
{
	protected $_table = 'sys_language';
	protected $_primaryKey = 'code';
	protected $_intFields = ['lan_id'];
}