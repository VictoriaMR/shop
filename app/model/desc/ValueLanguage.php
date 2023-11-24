<?php

namespace app\model\desc;
use app\model\Base;

class ValueLanguage extends Base
{
	protected $_table = 'desc_value_language';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'descv_id'];
}