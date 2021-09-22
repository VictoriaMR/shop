<?php

namespace app\model\order;
use app\model\Base;

class Order extends Base
{
	protected $_table = '`order`';
	protected $_primaryKey = 'order_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';

	const STATUS_CANCEL = 0;//取消订单
	const STATUS_WAIT_PAY = 1; //等待支付
	const STATUS_PAIED = 2;//待发货
	const STATUS_SHIPPED = 3;//运输中
	const STATUS_FINISHED = 4;//已完成
	const STATUS_PART_REFUND = 5;//部分退款
	const STATUS_FULL_REFUND = 6;//全部退款
	const STATUS_REFUNDING = 7; //退款中

	const ORDER_WAIT_PAY_TIME = 7 * 24 * 3600;
}