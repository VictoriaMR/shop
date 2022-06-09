<?php

namespace app\model\attr;
use app\model\Base;

class ValueLanguage extends Base
{
	protected $_table = 'attr_value_language';
	protected $_intFields = ['item_id', 'attv_id'];
}