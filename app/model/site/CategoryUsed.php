<?php

namespace app\model\site;
use app\model\Base;

class CategoryUsed extends Base
{
	protected $_table = 'site_category_used';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'site_id', 'cate_id', 'attach_id', 'sort', 'sale_total', 'visit_total'];
}