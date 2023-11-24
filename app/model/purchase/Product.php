<?php

namespace app\model\purchase;
use app\model\Base;

class Product extends Base
{
	protected $_table = 'purchase_product';
	protected $_addTime = 'add_time';
	protected $_intFields = ['purchase_product_id', 'purchase_channel_id', 'mem_id', 'purchase_shop_id', 'status'];

	const STATUS_NORMAL = 0;
	const STATUS_SET = 1;
	const STATUS_USED = 2;
	const STATUS_FAIL = 3;

	public function getStatusList()
    {
        return [
        	self::STATUS_NORMAL => '未使用',
        	self::STATUS_SET => '已上传',
        	self::STATUS_USED => '已使用',
        	self::STATUS_FAIL => '已废弃',
        ];
    }
}