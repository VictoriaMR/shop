<?php 

namespace app\service\order;
use app\service\Base;

class StatusHistory extends Base
{
	public function addLog($orderId, $status, $lanId='', $replace=[])
	{
		if (empty($orderId)) {
			return false;
		}
		$data = [
			'order_id' => $orderId,
			'status' => $status,
			'info' => $this->getStatusText($status, $lanId, $replace),
		];
		return $this->insert($data);
	}

	protected function getStatusText($status, $replace, $lanId)
	{
		$arr = [
			0 => 'cancel_order',
			1 => 'create_order',
			2 => 'paid_success',
			3 => 'shipping_start',
			4 => 'complete_order',
			5 => 'order_refunded',
			6 => 'order_refunded',
			7 => 'order_refunding',
		];
		return appT($arr[$status], $lanId, $replace);
	}
}