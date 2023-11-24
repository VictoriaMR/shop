<?php

namespace app\model\purchase;
use app\model\Base;

class Shop extends Base
{
	protected $_table = 'purchase_shop';
	protected $_addTime = 'add_time';
	protected $_intFields = ['purchase_shop_id', 'purchase_channel_id'];
}