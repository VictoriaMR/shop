<?php

namespace app\model\category;
use app\model\Base;

class Language extends Base
{
	protected $_table = 'category_language';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'cate_id'];
}