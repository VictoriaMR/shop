<?php

namespace app\model;
use app\model\Base;

class Order extends Base
{
	protected $_table = 'order';
	protected $_primaryKey = 'order_id';
	protected $_addTime = 'add_time';
	protected $_updateTime = 'update_time';

	const STATUS_CANCEL = 0;//取消订单
	const STATUS_WAIT_PAY = 1; //等待支付
	const STATUS_PAIED = 2;//待发货
	const STATUS_SHIPPED = 3;//运输中
	const STATUS_FINISHED = 4;//已完成
	const STATUS_REFUND = 5;//退款
}