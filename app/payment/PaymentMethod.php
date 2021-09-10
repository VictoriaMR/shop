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
			self::PAYMENT_TYPE_PAYPAL => [
				'type' => self::PAYMENT_TYPE_PAYPAL,
				'name' => 'PayPal',
				'class' => 'app/payment/paypal/PayPal',
			],
			self::PAYMENT_TYPE_STRIPE_CARD => [
				'type' => self::PAYMENT_TYPE_STRIPE_CARD,
				'name' => 'Stripe Card',
				'class' => 'app/payment/stripe/Card',
			],
			self::PAYMENT_TYPE_STRIPE_WALLET => [
				'type' => self::PAYMENT_TYPE_STRIPE_WALLET,
				'name' => 'Stripe Wallet',
				'class' => 'app/payment/stripe/Wallet',
			],
		];
	}

}