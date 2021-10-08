<?php

namespace frame;

class Http 
{
	public function get($url, $params='', $header=[], $timeout=5, $options=[])
	{
		return self::send($url, $params, 'GET', $header, $timeout, $options);
	}

	public function post($url, $params='', $header=[], $timeout=5, $options=[])
	{
		return self::send($url, $params, 'POST', $header, $timeout, $options);
	}

	private function send($url, $params='', $method='GET', $header=[], $timeout=5, $options=[])
	{
		$ch = curl_init();
		$opt = array(
			CURLOPT_HEADER => 0,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_TIMEOUT => $timeout,
		);
		if (is_array($params)) {
			$params = http_build_query($params);
		}
		switch ($method) {
			case 'GET':
				$opt[CURLOPT_HTTPGET] = 1;
				if ($params) $url .= '?'.$params;
				break;
			case 'POST':
				$opt[CURLOPT_POST] = 1;
				$opt[CURLOPT_POSTFIELDS] = $params;
				break;
			case 'HEAD':
				$opt[CURLOPT_NOBODY] = 1;
				break;
		}
		$opt[CURLOPT_URL] = $url;
		curl_setopt_array($ch, $opt+$options);
		$result = curl_exec($ch);
		if (!$result = curl_exec($ch)) {
			$this->setError([
				'errno' => curl_errno($ch),
				'msg' => curl_error($ch),
				'info' => curl_getinfo($ch),
			]);
		}
		curl_close($ch);
		return $result;
	}

	protected function headerFormat($header)
	{
		$tempData = [];
		foreach ($header as $key => $value) {
			$tempData[] = $key.':'.$value;
		}
		return $tempData;
	}

	protected function setError($data=[])
	{
		foreach ($data as $key=>$value) {
			$this->$key = $value;
		}
		return true;
	}

	public function getError($name)
	{
		return $this->$name ?? '';
	}
}