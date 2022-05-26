<?php

namespace app\model\attr;
use app\model\Base;

class NameLanguage extends Base
{
	protected $_table = 'attr_name_language';
	protected $_intFields = ['item_id', 'attrn_id'];
}