<?php

namespace app\model\site;
use app\model\Base;

class Site extends Base
{
	protected $_table = 'site';
	protected $_primaryKey = 'site_id';
	protected $_addTime = 'add_time';
}