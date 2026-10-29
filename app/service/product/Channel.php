<?php

namespace app\service\product;
use app\service\Base;

class Channel extends Base
{
	public function getChannelId($url)
	{
		if (empty($url)) {
			return false;
		}
		$host = (string)(parse_url((string)$url, PHP_URL_HOST) ?? '');
		if ($host === '') {
			return false;
		}
		return match (true) {
			str_contains($host, 'taobao.com') => 6051,
			str_contains($host, 'tmall.com') => 6052,
			str_contains($host, '1688.com') => 6053,
			default => false,
		};
	}

	public function getItemId($url, $channelId = 0)
	{
		if (!$channelId) {
			$channelId = $this->getChannelId($url);
		}
		if (!$channelId) {
			return false;
		}
		switch ($channelId) {
			case 6051:
			case 6052:
				parse_str((string)(parse_url((string)$url, PHP_URL_QUERY) ?? ''), $match);
				return $match['id'] ?? false;
			case 6053:
				preg_match('/^https?:\/\/detail\.1688\.com\/offer\/(\d+)\.html/i', (string)$url, $match);
				return $match[1] ?? false;
		}
		return false;
	}
}
