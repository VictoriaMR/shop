<?php

namespace app\payment;

class PayPal extends PaymentMethod
{
	public function pay($data)
	{
		$this->setOrderId($orderId);
		if (empty($this->getConfig())) {
			return false;
		}
		$path = $this->getTemplatePath();
		if (empty($path)) {
			return false;
		}
		// dd($this->getOrderInfo());
		return $this->getTemplate($path, ['config'=>$this->config, 'orderData'=>$this->getOrderInfo(), 'method'=>$this->type]);
	}
}