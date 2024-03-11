<?php

namespace app\payment\stripe;
use app\payment\Stripe;

class Wallet extends Stripe
{
	protected $id = 'stripe_wallet';
	protected $type = self::PAYMENT_TYPE_STRIPE_WALLET;

	public function pay($data)
	{
		if (empty($this->getConfig())) {
			return false;
		}
		if (!empty($data['order_id'])) {
			$this->setOrderId($data['order_id']);
			$orderInfo = $this->getOrderInfo();
			if (empty($orderInfo)) {
				return false;
			}
			$data['currency'] = $orderInfo['base']['currency'];
			$data['country_code2'] = $orderInfo['shipping_address']['country_code2'];
			$data['order_total'] = $orderInfo['base']['order_total'];
		}
		$path = $this->getTemplatePath(false);
		if (empty($path)) {
			return false;
		}
		$data['order_total_format'] = make('app/service/currency/Currency')->priceSymbol(2).$data['order_total'];
		$data['order_total'] = $this->getAmount($data['order_total'], $data['currency']);
		$data['currency'] = strtolower($data['currency']);
		$data['config'] = $this->config;
		$data['method'] = $this->type;
		return $this->getTemplate($path, $data);
	}
}