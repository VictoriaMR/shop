<?php

namespace app\model\site;
use app\model\Base;

class LanguageUsed extends Base
{
	protected $_table = 'site_language_used';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'site_id', 'sort'];
}