<?php

namespace app\payment;

abstract class PaymentMethod
{
	//支付类型ID
	protected $id;
	protected $type;
	protected $config;
	protected $orderInfo;
	protected $orderId;
	protected $error = [];

	//支付方式
	const PAYMENT_TYPE_PAYPAL = 1; //paypal支付
	const PAYMENT_TYPE_STRIPE_CARD = 2; //stripe card 支付
	const PAYMENT_TYPE_STRIPE_WALLET = 3; //stripe wallet 支付

	protected function getConfig()
	{
		if (empty($this->config)) {

		}
		return $this->config;
	}

	protected function setOrderId($orderId)
	{
		$this->orderId = $orderId;
	}

	protected function getOrderId()
	{
		return $this->orderId;
	}

	protected function getOrderInfo()
	{
		if (empty($this->orderInfo)) {
			$this->orderInfo = make('app/service/order/Order')->getInfo($this->getOrderId()); 
		}
		return $this->orderInfo;
	}

	protected function setError($msg='')
	{
		if (empty($msg)) {
			$this->error = [];
		} else {
			$this->error[] = $msg;
		}
	}

	protected function getError()
	{
		return $this->getError();
	}

	public static function methodList()
	{
		return [
			'paypal' => [
				'type' => self::PAYMENT_TYPE_PAYPAL,
				'name' => 'PayPal',
				'class' => 'app/payment/paypal/PayPal',
			],
			'stripe_card' => [
				'type' => self::PAYMENT_TYPE_STRIPE_CARD,
				'name' => 'Stripe Card',
				'class' => 'app/payment/stripe/Card',
			],
			'stripe_wallet' => [
				'type' => self::PAYMENT_TYPE_STRIPE_WALLET,
				'name' => 'Stripe Wallet',
				'class' => 'app/payment/stripe/Wallet',
			],
		];
	}

}