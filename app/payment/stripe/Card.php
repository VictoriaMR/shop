<?php

namespace app\payment\stripe;
use app\payment\Stripe;

class Card extends Stripe
{
	protected $id = 'stripe_card';
	protected $type = self::PAYMENT_TYPE_STRIPE_CARD;

	public function pay($data)
	{
		if (empty($this->getConfig())) {
			return appT('payment_method_error');
		}
		if (!is_array($data)) {
			$this->setOrderId($data);
			$orderInfo = $this->getOrderInfo();
			if (empty($orderInfo)) {
				return appT('payment_method_error');
			}
			$data = [];
			$data['order_id'] = $orderInfo['base']['order_id'];
			$data['currency'] = $orderInfo['base']['currency'];
			$data['country_code2'] = $orderInfo['shipping_address']['country_code2'];
			$data['order_total'] = $orderInfo['base']['order_total'];
			$data['shipping_address'] = $orderInfo['shipping_address'];
			$data['billing_address'] = $orderInfo['billing_address'];
		}
		$path = $this->getTemplatePath();
		if (empty($path)) {
			return appT('payment_method_error');
		}
		$data['order_total_format'] = make('app/service/Currency')->priceSymbol(2).$data['order_total'];
		$data['config'] = $this->config;
		$data['method'] = $this->type;
		$data['order_total'] = $this->getAmount($data['order_total'], $data['currency']);
		return $this->getTemplate($path, $data);
	}
}