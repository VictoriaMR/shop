<?php

namespace app\model\order;
use app\model\Base;

class Order extends Base
{
	protected $_table = 'order';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';
	protected $_intFields = ['order_id', 'site_id', 'mem_id', 'status', 'coupon_id', 'payment_id', 'isMobile()', 'is_review', 'is_delete'];

	const STATUS_CANCEL = 0;//取消订单
	const STATUS_WAIT_PAY = 1; //等待支付
	const STATUS_PAIED = 2;//待发货
	const STATUS_SHIPPED = 3;//运输中
	const STATUS_FINISHED = 4;//已完成
	const STATUS_PART_REFUND = 5;//部分退款
	const STATUS_FULL_REFUND = 6;//全部退款
	const STATUS_REFUNDING = 7; //退款中

	const ORDER_WAIT_PAY_TIME = 7 * 24 * 3600; //最大订单待支付时间

	public function orderStatus($status=null)
	{
		$arr = [
			self::STATUS_CANCEL => '取消订单',
			self::STATUS_WAIT_PAY => '等待支付',
			self::STATUS_PAIED => '等待发货',
			self::STATUS_SHIPPED => '运输中',
			self::STATUS_FINISHED => '已完成',
			self::STATUS_PART_REFUND => '部分退款',
			self::STATUS_FULL_REFUND => '全部退款',
			self::STATUS_REFUNDING => '退款中',
		];
		if (is_null($status)) return $arr;
		return $arr[$status] ?? '';
	}
}