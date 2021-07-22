<?php

namespace app\model;
use app\model\Base;

class ProductSpu extends Base
{
	protected $_table = 'product_spu';
	protected $_primaryKey = 'spu_id';

	const STATUS_CLOSE = 0;
	const STATUS_OPEN = 1;

	public function getStatusList()
	{
		return [
			self::STATUS_CLOSE => '下架',
			self::STATUS_OPEN => '上架',
		];
	}
}