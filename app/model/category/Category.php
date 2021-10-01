<?php

namespace app\model\category;
use app\model\Base;

class Category extends Base
{
	protected $_table = 'category';
	protected $_primaryKey = 'cate_id';
	protected $_intFields = ['cate_id', 'parent_id', 'status'];
}