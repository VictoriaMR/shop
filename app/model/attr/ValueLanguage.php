<?php

namespace app\model\attr;
use app\model\Base;

class ValueLanguage extends Base
{
	protected $_table = 'attrvalue_language';
	protected $_intFields = ['item_id', 'attv_id'];
}