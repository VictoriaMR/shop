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
				return '';
			}
			$data['currency'] = $orderInfo['base']['currency'];
			$data['country_code2'] = $orderInfo['shipping_address']['country_code2'];
			$data['order_total'] = $orderInfo['base']['order_total'];
		}
		$path = $this->getTemplatePath(false);
		if (empty($path)) {
			return false;
		}
		// dd($this->getOrderInfo());
		return $this->getTemplate($path, [
			'order_id' => $data['order_id'] ?? 0,
			'config'=>$this->config, 
			'method'=>$this->type,
			'order_total' => $this->getAmount($data['order_total'], $data['currency']), 
			'currency' => strtolower($data['currency']), 
			'country_code2' => 'US', 
			'data' => $data,
		]);
	}
}