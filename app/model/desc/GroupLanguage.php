<?php

namespace app\model\desc;
use app\model\Base;

class GroupLanguage extends Base
{
	protected $_table = 'desc_group_language';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'descg_id'];
}