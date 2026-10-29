<?php

namespace app\payment;

class PayPal extends PaymentMethod
{
	public function pay($orderId)
	{
		$this->setOrderId($orderId);
		if (empty($this->getConfig())) {
			return false;
		}
		$path = method_exists($this, 'getTemplatePath') ? $this->getTemplatePath() : '';
		if (empty($path)) {
			return false;
		}
		return $this->getTemplate($path, [
			'config' => $this->config,
			'orderData' => $this->getOrderInfo(),
			'method' => $this->type,
		]);
	}
}
