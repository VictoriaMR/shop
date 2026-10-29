<?php

namespace app\payment;

abstract class Stripe extends PaymentMethod
{
	protected array $request_body = [];

	public function getPaymentIntentByOrderId($orderId)
	{
		$this->setOrderId($orderId);
		$orderData = $this->getOrderInfo();
		if (empty($orderData)) {
			return false;
		}

		$payDes = 'Purchase From ' . site()->getName();
		$baseInfo = $orderData['base_info'] ?? $orderData;
		$request_body = [
			'amount' => $this->getAmount($baseInfo['order_total'] ?? 0, $baseInfo['currency'] ?? 'USD'),
			'currency' => strtolower($baseInfo['currency'] ?? 'usd'),
			'metadata' => [
				'site_id' => siteId(),
				'order_id' => $baseInfo['order_id'] ?? $orderId,
				'type_id' => $this->id,
			],
			'shipping' => [
				'address' => [
					'city' => $baseInfo['city'] ?? '',
					'country' => $baseInfo['country_code2'] ?? '',
					'state' => $baseInfo['state'] ?? '',
					'postal_code' => $baseInfo['postcode'] ?? '',
					'line1' => $baseInfo['address_line1'] ?? '',
					'line2' => $baseInfo['address_line2'] ?? '',
				],
				'name' => trim(($baseInfo['first_name'] ?? '') . ' ' . ($baseInfo['last_name'] ?? '')),
				'phone' => $baseInfo['phone'] ?? '',
			],
			'description' => $payDes,
			'statement_descriptor' => $payDes,
		];
		$request_body = $request_body + $this->request_body;
		return $this->request($this->getRequestUrl('payment_intents'), $request_body);
	}

	public function getAmount($amount, $currency)
	{
		$currency = strtoupper((string)$currency);
		if (in_array($currency, $this->getZeroDecimalCurrency(), true)) {
			return (int)ceil((float)$amount);
		}
		if (in_array($currency, $this->getSpecialCurrency(), true)) {
			return (int)(ceil((float)$amount) * 100);
		}
		return (int)round((float)$amount * 100);
	}

	protected function getZeroDecimalCurrency(): array
	{
		return ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];
	}

	protected function getSpecialCurrency(): array
	{
		return ['HUF', 'UGX'];
	}

	protected function getSupportCurrency(): array
	{
		return ['USD', 'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 'BGN', 'BIF', 'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BWP', 'BZD', 'CAD', 'CDF', 'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CVE', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD', 'EGP', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GIP', 'GMD', 'GNF', 'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'ISK', 'JMD', 'JPY', 'KES', 'KGS', 'KHR', 'KMF', 'KRW', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'MAD', 'MDL', 'MGA', 'MKD', 'MMK', 'MNT', 'MOP', 'MRO', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'PYG', 'QAR', 'RON', 'RSD', 'RUB', 'RWF', 'SAR', 'SBD', 'SCR', 'SEK', 'SGD', 'SHP', 'SLL', 'SOS', 'SRD', 'STD', 'SZL', 'THB', 'TJS', 'TOP', 'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'UGX', 'UYU', 'UZS', 'VND', 'VUV', 'WST', 'XAF', 'XCD', 'XOF', 'XPF', 'YER', 'ZAR', 'ZMW'];
	}

	protected function getRequestUrl(string $suffix = ''): string
	{
		return 'https://api.stripe.com/v1/' . $suffix;
	}

	protected function request(string $url, $body = '', array $header = [], string $method = 'post')
	{
		if (!empty($body) && is_array($body)) {
			$body = json_encode($body, JSON_UNESCAPED_UNICODE);
		}
		$header['Authorization'] = 'Bearer ' . ($this->config['secret_key'] ?? '');
		$header['Content-Type'] = 'application/json';

		$rst = frame('Http')->post($url, $body, $header);

		return isArray($rst);
	}
}
