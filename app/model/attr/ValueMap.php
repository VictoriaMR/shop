<?php

namespace app\model\attr;
use app\model\Base;

class ValueMap extends Base
{
	protected $_table = 'attr_value_map';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'attrv_id'];
}