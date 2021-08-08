<?php

namespace app\model\product;
use app\model\Base;

class Spu extends Base
{
	protected $_table = 'product_spu';
	protected $_primaryKey = 'spu_id';

	const STATUS_CLOSE = 0;
	const STATUS_OPEN = 1;
	const CACHE_INFO_KEY = 'spu-info:';
	const CACHE_EXPIRE_TIME = 3600*24;

	public function getStatusList($status=null)
	{
		$arr = [
			self::STATUS_CLOSE => '下架',
			self::STATUS_OPEN => '上架',
		];
		if (is_null($status)) {
			return $arr;
		}
		return $arr[$status] ?? '';
	}
}