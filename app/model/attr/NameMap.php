<?php

namespace app\model\attr;
use app\model\Base;

class NameMap extends Base
{
	protected $_table = 'attr_name_map';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'attrn_id'];
}