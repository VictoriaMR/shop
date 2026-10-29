<?php

namespace app\service\log;
use app\service\Base;

class Order extends Base
{
	public function add($info, $orderId, $orderProductId = 0)
	{
		if (empty($info)) return false;
		return $this->insertData([
			'order_id' => $orderId,
			'order_product_id' => $orderProductId,
			'mem_id' => userId(),
			'info' => trim((string)$info),
		]);
	}

	public function addLog($info, $orderId = 0, $orderProductId = 0)
	{
		return $this->add($info, $orderId, $orderProductId);
	}
}
