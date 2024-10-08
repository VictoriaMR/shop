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

	abstract public function pay($orderId);
	// abstract public function return($orderId);

	protected function getConfig()
	{
		if (empty($this->config)) {
			$config = service('payment/Used')->loadData(['site_id'=>siteId(),'type'=>$this->type], 'payment_id');
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
				'name' => appT('Credit Card'),
				'class' => 'app/payment/stripe/Card',
			],
			self::PAYMENT_TYPE_STRIPE_WALLET => [
				'type' => self::PAYMENT_TYPE_STRIPE_WALLET,
				'name' => 'Stripe Wallet',
				'class' => 'app/payment/stripe/Wallet',
				'in_list' => false,
			],
		];
	}

	public static function sortedMethodList()
	{
		$methodList = self::methodList();
		//站点使用的支付方式
		$usedList = service('payment/Used')->getListData(['site_id'=>siteId()], 'type', 0, 0, ['sort'=>'asc']);
		if (empty($usedList)) {
			return [];
		}
		$sortedMethodList = [];
		foreach ($usedList as $value) {
			if (isset($methodList[$value['type']]['in_list']) && !$methodList[$value['type']]['in_list']) {
				continue;
			}
			$sortedMethodList[$value['type']] = $methodList[$value['type']];
		}
		return $sortedMethodList;
	}

	protected function getTemplatePath($driver=true)
	{
		$path = ROOT_PATH.type().DS.'view'.DS.($driver ? (isMobile() ? 'mobile' : 'computer') : 'common').DS.'common'.DS.'payment'.DS.$this->id.'.php';
		if (is_file($path)) {
			return $path;
		}
		$path = ROOT_PATH.'template'.DS.'payment'.DS.($driver ? (isMobile() ? 'mobile' : 'computer') : 'common').DS.$this->id.'.php';
		if (is_file($path)) {
			return $path;
		}
		return false;
	}

	protected function getTemplate($path, $data=[])
	{
		return frame('View')->getContent($path, $data);
	}
}