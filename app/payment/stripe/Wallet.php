<?php

namespace app\payment\stripe;
use app\payment\Stripe;

class Wallet extends Stripe
{
	protected $id = 'stripe_wallet';
	protected $type = self::PAYMENT_TYPE_STRIPE_WALLET;
}