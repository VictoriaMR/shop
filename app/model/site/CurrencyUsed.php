<?php

namespace app\model\site;
use app\model\Base;

class CurrencyUsed extends Base
{
	protected $_table = 'site_currency_used';
	protected $_primaryKey = 'item_id';
	protected $_intFields = ['item_id', 'site_id', 'sort'];
}