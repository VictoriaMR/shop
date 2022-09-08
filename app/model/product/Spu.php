<?php

namespace app\model\product;
use app\model\Base;

class Spu extends Base
{
	protected $_table = 'product_spu';
	protected $_primaryKey = 'spu_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['spu_id', 'site_id', 'cate_id', 'status', 'gender', 'rank', 'attach_id', 'sale_total', 'visit_total', 'free_ship'];

	const STATUS_CLOSE = 0;
	const STATUS_OPEN = 1;
	const STATUS_OUT_OF_STOCK = 2;//无库存
	const CACHE_INFO_KEY = 'spu:info:';
	const CACHE_EXPIRE_TIME = 3600*24;//一天有效期

	public function getStatusList($status=null)
	{
		$arr = [
			self::STATUS_CLOSE => '未上架',
			self::STATUS_OPEN => '销售中',
			self::STATUS_OUT_OF_STOCK => '已下架',
		];
		if (is_null($status))return $arr;
		return $arr[$status] ?? '';
	}
}