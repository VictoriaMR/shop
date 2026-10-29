<?php

namespace frame;

class Http
{
	protected array $error = [];

	public function get($url, $params = '', array $header = [], int $timeout = 5, array $options = [])
	{
		return $this->send($url, $params, 'GET', $header, $timeout, $options);
	}

	public function post($url, $params = '', array $header = [], int $timeout = 5, array $options = [])
	{
		return $this->send($url, $params, 'POST', $header, $timeout, $options);
	}

	private function send($url, $params = '', string $method = 'GET', array $header = [], int $timeout = 5, array $options = [])
	{
		$ch = curl_init();
		$opt = [
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => $timeout,
		];
		if ($header) {
			$opt[CURLOPT_HTTPHEADER] = $this->headerFormat($header);
		}
		if (is_array($params)) {
			$params = http_build_query($params);
		}
		match (strtoupper($method)) {
			'GET' => (function () use (&$opt, &$url, $params) {
				$opt[CURLOPT_HTTPGET] = 1;
				if ($params) {
					$url .= (str_contains($url, '?') ? '&' : '?') . $params;
				}
			})(),
			'POST' => (function () use (&$opt, $params) {
				$opt[CURLOPT_POST] = 1;
				$opt[CURLOPT_POSTFIELDS] = $params;
			})(),
			'HEAD' => $opt[CURLOPT_NOBODY] = 1,
			default => $opt[CURLOPT_CUSTOMREQUEST] = $method,
		};
		$opt[CURLOPT_URL] = $url;
		curl_setopt_array($ch, $opt + $options);
		$result = curl_exec($ch);
		if ($result === false) {
			$this->setError([
				'errno' => curl_errno($ch),
				'msg' => curl_error($ch),
				'info' => curl_getinfo($ch),
			]);
		}
		return $result;
	}

	protected function headerFormat(array $header): array
	{
		$tempData = [];
		foreach ($header as $key => $value) {
			$tempData[] = is_int($key) ? $value : $key . ':' . $value;
		}
		return $tempData;
	}

	protected function setError(array $data = []): bool
	{
		$this->error = $data;
		return true;
	}

	public function getError(string $name = '')
	{
		if ($name === '') {
			return $this->error;
		}
		return $this->error[$name] ?? '';
	}
}
