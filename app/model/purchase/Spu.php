<?php

namespace app\model\purchase;
use app\model\Base;

class Spu extends Base
{
	protected $_table = 'purchase_spu';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['purchase_spu_id', 'purchase_channel_id', 'mem_id', 'purchase_shop_id', 'status', 'sale_count'];

	const STATUS_NORMAL = 0;
	const STATUS_SET = 1;
	const STATUS_USED = 2;
	const STATUS_FAIL = 3;
}