<?php

namespace app\payment\stripe;
use app\payment\Stripe;

class Card extends Stripe
{
	protected $id = 'stripe_card';
	protected $type = self::PAYMENT_TYPE_STRIPE_CARD;

	public function pay($orderId)
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