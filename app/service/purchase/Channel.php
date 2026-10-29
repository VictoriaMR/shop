<?php

namespace app\service\purchase;
use app\service\Base;

class Channel extends Base
{
	public function getChannel($type)
	{
		return $this->getConst('CHANNEL_' . strtoupper((string)$type));
	}

	public function getChannelId($url)
	{
		if (preg_match('/(http:|https:)?\/\/.*(taobao|tmall|1688)\.com/', (string)$url, $match)) {
			return $this->getChannel($match[2]);
		}
		return false;
	}

	public function getItemId($url)
	{
		if (preg_match('/(http:|https:)?\/\/(item|detail)\.(taobao|tmall|1688)\.com\/(item|item_o|offer)\/?(\d+)?\.(htm|html)(?:.*?[?&]id=(\d+))?/', (string)$url, $match)) {
			return ($match[5] ?? '') !== '' ? $match[5] : ($match[7] ?? false);
		}
		return false;
	}
}
