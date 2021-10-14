<?php

namespace app\model\attr;
use app\model\Base;

class Bute extends Base
{
	protected $_table = 'attribute';
	protected $_primaryKey = 'attr_id';
	protected $_intFields = ['attr_id', 'status'];
}