<?php
namespace app\service\tool;

use \MaxMind\Db\Reader;

class IP
{
	const DB_PATH = ROOT_PATH . 'MaxMind' . DS . 'GeoLite2-City.mmdb';
	const DB_DEFAULT_LANGUAGE = 'en';

	private ?Reader $maxMindInstance = null;

	public function isIp($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP);
	}

	public function isIpv4($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
	}

	public function isIpv6($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	}

	public function ipToBin($ip): string
	{
		$bin_str = inet_pton($ip);
		if ($bin_str === false) {
			return '';
		}
		$bin = '';
		foreach (str_split($bin_str) as $char) {
			$bin .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
		}
		return $bin;
	}

	public function checkIp($ip, $cidr): bool
	{
		if (!str_contains($cidr, '/')) {
			$len = $this->isIpv6($ip) ? 128 : 32;
			$cidr .= '/' . $len;
		}
		[$subnet, $bits] = explode('/', $cidr);
		$bits = (int)$bits;
		$ip_bin = substr($this->ipToBin($ip), 0, $bits);
		$subnet_bin = substr($this->ipToBin($subnet), 0, $bits);
		return $ip_bin === $subnet_bin;
	}

	public function getInfo($ip)
	{
		if (str_contains((string)$ip, '/')) {
			$ip = explode('/', (string)$ip)[0];
		}
		if (!$this->isIp($ip)) {
			return false;
		}
		if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			$ip = '66.249.65.172';
		}
		$cacheKey = 'IP:' . $ip;
		$cached = redis(4)->get($cacheKey);
		if (!empty($cached) && is_array($cached)) {
			return $cached;
		}
		$data = $this->maxMindInstance()?->get($ip);
		if (empty($data)) {
			return false;
		}
		$lanCode = lanId('code');
		$returnData = [];
		// 洲
		if (isset($data['continent'])) {
			$returnData['continent_code'] = $data['continent']['code'] ?? '';
			$returnData['continent'] = $data['continent']['names'][$lanCode] ?? ($data['continent']['names'][self::DB_DEFAULT_LANGUAGE] ?? '');
		}
		// 国家
		if (isset($data['country'])) {
			$returnData['country_code'] = $data['country']['iso_code'] ?? '';
			$returnData['country'] = $data['country']['names'][$lanCode] ?? ($data['country']['names'][self::DB_DEFAULT_LANGUAGE] ?? '');
		}
		// 州
		if (!empty($data['subdivisions'])) {
			$tmp = array_last($data['subdivisions']);
			$returnData['state_code'] = $tmp['iso_code'] ?? '';
			$returnData['state'] = $tmp['names'][$lanCode] ?? ($tmp['names'][self::DB_DEFAULT_LANGUAGE] ?? '');
		}
		// 城市
		if (isset($data['city'])) {
			$returnData['city'] = $data['city']['names'][$lanCode] ?? ($data['city']['names'][self::DB_DEFAULT_LANGUAGE] ?? '');
		}
		// 邮编
		$returnData['postal'] = $data['postal']['code'] ?? '';
		// 定位
		if (isset($data['location'])) {
			$returnData['accuracy_radius'] = $data['location']['accuracy_radius'] ?? null;
			$returnData['latitude'] = $data['location']['latitude'] ?? null;
			$returnData['longitude'] = $data['location']['longitude'] ?? null;
			$returnData['time_zone'] = $data['location']['time_zone'] ?? null;
		}
		redis(4)->set($cacheKey, $returnData, 24 * 3600);
		return $returnData;
	}

	public function getCountryCode(): string
	{
		if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
			$code2 = $_SERVER['HTTP_CF_IPCOUNTRY'];
		} else {
			$info = $this->getInfo(frame('IP')->getIp());
			$code2 = is_array($info) ? ($info['country_code'] ?? '') : '';
		}
		return empty($code2) ? 'US' : strtoupper($code2);
	}

	private function maxMindInstance(): ?Reader
	{
		if ($this->maxMindInstance === null && is_file(self::DB_PATH)) {
			$this->maxMindInstance = new Reader(self::DB_PATH);
		}
		return $this->maxMindInstance;
	}
}
