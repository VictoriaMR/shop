<?php

namespace app\model\category;
use app\model\Base;

class Language extends Base
{
	protected $_table = 'category_language';
	protected $_intFields = ['item_id', 'cate_id'];
}