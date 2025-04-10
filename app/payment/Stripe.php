<?php

namespace app\payment;

abstract class Stripe extends PaymentMethod
{
	public function getPaymentIntentByOrderId($orderId)
	{
		$this->setOrderId($orderId);
		$orderInfo = $this->getOrderInfo();

		$payDes = 'Purchase From ' . site()->getConfig('name', $this->getSiteId());
		$request_body = [
			'amount' => $this->getAmount($orderData['base_info']['order_total'], $orderData['base_info']['currency']),
			'currency' => strtolower($orderData['base_info']['currency']),
			'metadata' => [
				'site_id' => $this->getSiteId(),
				'order_id' => $orderData['base_info']['order_id'],
				'type_id' => $this->id,
			],
			'shipping' => [
				'address' => [
					'city' => $orderData['base_info']['city'],
					'country' => $orderData['base_info']['country_code2'],
					'state' => $orderData['base_info']['state'],
					'postal_code' => $orderData['base_info']['postcode'],
					'line1' => $orderData['base_info']['address_line1'],
					'line2' => $orderData['base_info']['address_line2'],
				],
				'name' => $orderData['base_info']['first_name'] . ' ' . $orderData['base_info']['last_name'],
				'phone' => $orderData['base_info']['phone'],
			],
			'description' => $payDes,
			'statement_descriptor' => $payDes,
		];
		$request_body = $request_body + $this->request_body;

	}

	public function getAmount($amount, $currency)
	{
		$currency = strtoupper($currency);
		if (in_array($currency, $this->getZeroDecimalCurrency())) {
			return (int) (ceil($amount));
		} elseif (in_array($currency, $this->getSpecialCurrency())) {
			return (int) (ceil($amount) * 100);
		} else {
			return $amount * 100;
		}
	}

	protected function getZeroDecimalCurrency()
	{
		return ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];
	}

	protected function getSpecialCurrency()
	{
		return ['HUF', 'UGX'];
	}

	protected function getSupportCurrency()
	{
		return ['USD', 'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 'BGN', 'BIF', 'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BWP', 'BZD', 'CAD', 'CDF', 'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CVE', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD', 'EGP', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GIP', 'GMD', 'GNF', 'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'ISK', 'JMD', 'JPY', 'KES', 'KGS', 'KHR', 'KMF', 'KRW', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'MAD', 'MDL', 'MGA', 'MKD', 'MMK', 'MNT', 'MOP', 'MRO', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'PYG', 'QAR', 'RON', 'RSD', 'RUB', 'RWF', 'SAR', 'SBD', 'SCR', 'SEK', 'SGD', 'SHP', 'SLL', 'SOS', 'SRD', 'STD', 'SZL', 'THB', 'TJS', 'TOP', 'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'UGX', 'UYU', 'UZS', 'VND', 'VUV', 'WST', 'XAF', 'XCD', 'XOF', 'XPF', 'YER', 'ZAR', 'ZMW'];
	}

	protected function getRequestUrl($suffix='')
	{
		return 'https://api.stripe.com/v1/'.$suffix;
	}

	protected function request($url, $body='', $header=[], $method='post')
	{
		if (!empty($body) && is_array($body)) {
			$body = json_decode($body, JSON_UNESCAPED_UNICODE);
		}
		$header['Authorization'] = 'Bearer '.$this->config['secret_key'];
		$header['Content-Type'] = 'application/json';

		$rst = frame('Http')->post($url, $body, $header);

		return isArray($rst);
	}
}