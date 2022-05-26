<?php

namespace app\model\desc;
use app\model\Base;

class NameLanguage extends Base
{
	protected $_table = 'desc_name_language';
	protected $_intFields = ['item_id', 'descn_id'];
}