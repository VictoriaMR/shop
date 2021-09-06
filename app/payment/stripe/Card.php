<?php

namespace app\payment\stripe;
use app\payment\Stripe;

class Card extends Stripe
{
	protected $id = 'stripe_card';
	protected $type = self::PAYMENT_TYPE_STRIPE_CARD;
}