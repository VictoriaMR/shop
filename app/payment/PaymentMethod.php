<?php

namespace app\payment;

abstract class PaymentMethod
{
	//支付类型ID
	protected $id;

	//支付方式
	const PAYMENT_TYPE_PAYPAL = 1; //paypal支付
}