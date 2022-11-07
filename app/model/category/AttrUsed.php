<?php

namespace app\model\category;
use app\model\Base;

class AttrUsed extends Base
{
	protected $_table = 'category_attr_used';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'cate_id', 'attrn_id', 'attrv_id', 'sort'];
}