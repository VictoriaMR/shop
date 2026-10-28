<?php

namespace app\payment;

abstract class PaymentMethod
{
	protected $id;
	protected $type;
	protected $config;
	protected $orderInfo;
	protected $orderId;
	protected $error = [];

	const PAYMENT_TYPE_PAYPAL = 1;
	const PAYMENT_TYPE_STRIPE_CARD = 2;
	const PAYMENT_TYPE_STRIPE_WALLET = 3;

	abstract public function pay($orderId);

	protected function getConfig()
	{
		if (empty($this->config)) {
			$config = service('payment/Used')->loadData(['site_id' => siteId(), 'type' => $this->type], 'payment_id');
			if (empty($config['payment_id'])) {
				return false;
			}
			$config = service('payment/Payment')->loadData($config['payment_id']);
			if (empty($config['status'])) {
				return false;
			}
			$this->config = $config;
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
			$this->orderInfo = service('order/Order')->getInfo($this->getOrderId());
		}
		return $this->orderInfo;
	}

	protected function setError($msg = '')
	{
		if (empty($msg)) {
			$this->error = [];
		} else {
			$this->error[] = $msg;
		}
	}

	protected function getError()
	{
		return $this->error;
	}

	public static function methodList()
	{
		return [
			self::PAYMENT_TYPE_PAYPAL => [
				'type'  => self::PAYMENT_TYPE_PAYPAL,
				'name'  => 'PayPal Express Checkout',
				'desc'  => 'Pay safely with your PayPal balance, bank account, or linked cards.',
				'badge' => 'Buyer Protection',
				'class' => 'app/payment/paypal/PayPal',
			],
			self::PAYMENT_TYPE_STRIPE_CARD => [
				'type'  => self::PAYMENT_TYPE_STRIPE_CARD,
				'name'  => 'Credit / Debit Card',
				'desc'  => 'Visa, Mastercard, American Express & Discover via 256-bit SSL Stripe gateway.',
				'badge' => 'Instant Confirmation',
				'class' => 'app/payment/stripe/Card',
			],
			self::PAYMENT_TYPE_STRIPE_WALLET => [
				'type'    => self::PAYMENT_TYPE_STRIPE_WALLET,
				'name'    => 'Stripe Wallet',
				'class'   => 'app/payment/stripe/Wallet',
				'in_list' => false,
			],
		];
	}

	public static function sortedMethodList()
	{
		$methodList = self::methodList();
		$usedList = service('payment/Used')->getListData(['site_id' => siteId()], 'type', 0, 0, ['sort' => 'asc']);
		if (empty($usedList)) {
			return [
				self::PAYMENT_TYPE_PAYPAL      => $methodList[self::PAYMENT_TYPE_PAYPAL],
				self::PAYMENT_TYPE_STRIPE_CARD => $methodList[self::PAYMENT_TYPE_STRIPE_CARD],
			];
		}
		$sortedMethodList = [];
		foreach ($usedList as $value) {
			if (isset($methodList[$value['type']]['in_list']) && !$methodList[$value['type']]['in_list']) {
				continue;
			}
			if (isset($methodList[$value['type']])) {
				$sortedMethodList[$value['type']] = $methodList[$value['type']];
			}
		}
		return $sortedMethodList;
	}
}
