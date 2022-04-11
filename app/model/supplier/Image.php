<?php

namespace app\model\supplier;
use app\model\Base;

class Image extends Base
{
	protected $_connect = 'static';
	protected $_table = 'supplier_image';
	protected $_primaryKey = 'supp_id';
	protected $_addTime = 'add_time';
}