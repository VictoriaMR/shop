<?php

namespace frame;

class Http 
{
	public function get($url, $params='', $header=[], $timeout=30, $options=[])
	{
		return self::send($url, $params, 'GET', $header, $timeout, $options);
	}

	public function post($url, $params='', $header=[], $timeout=30, $options=[])
	{
		return self::send($url, $params, 'POST', $header, $timeout, $options);
	}

	public function download($url, $savePath, $params='', $header=[], $timeout=3600)
	{
		if (!is_dir(dirname($savePath))) {
			Dir::create(dirname($savePath));
		}
		$ch = curl_init();
		$fp = fopen($savePath, 'wb');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header ? : ['Expect:']);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_BUFFERSIZE, 64000);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$res = curl_exec($ch);
		$curlInfo = curl_getinfo($ch);
		if (curl_errno($ch) || $curlInfo['http_code'] != 200) {
			curl_error($ch);
			@unlink($savePath);
			return false;
		} else {
			curl_close($ch);
		}
		fclose($fp);
		return $savePath;
	}

	private function send($url, $params='', $method='GET', $header=[], $timeout=30, $options=[])
	{
		$ch = curl_init();
		$opt = [];
		$opt[CURLOPT_COOKIEJAR] = $cookieFile ?? '';
		$opt[CURLOPT_COOKIEFILE] = $cookieFile ?? '';
		$opt[CURLOPT_CONNECTTIMEOUT] = $timeout;
		$opt[CURLOPT_TIMEOUT] = $timeout;
		$opt[CURLOPT_RETURNTRANSFER] = true;
		$opt[CURLOPT_HTTPHEADER] = $header ? : ['Expect:'];
		$opt[CURLOPT_FOLLOWLOCATION] = true;
		if (strpos($url, 'https') === 0) {
			$opt[CURLOPT_SSL_VERIFYPEER] = false;
			$opt[CURLOPT_SSL_VERIFYHOST] = 2;
		}
		if (is_array($params)) {
			$params = http_build_query($params);
		}
		switch (strtoupper($method)) {
			case 'GET':
				$opt[CURLOPT_URL] = $url . ($params ? '?'.$params : '');
				break;
			case 'POST':
				$opt[CURLOPT_POST] = true;
				$opt[CURLOPT_POSTFIELDS] = $params;
				$opt[CURLOPT_URL] = $url;
				break;
			default:
				return ['error'=>0, 'msg'=>'请求的方法不存在', 'info'=>[]];
				break;
		}
		curl_setopt_array($ch, (array) $opt + $options);
		$result = curl_exec($ch);
		$error = curl_error($ch);
		if ($result == false || !empty($error)) {
			$errno = curl_errno($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);
			return [
				'errno' => $errno,
				'msg' => $error,
				'info' => $info,
			];
		}
		curl_close($ch);
		return $result;
	}
}