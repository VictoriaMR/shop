<?php 

namespace app\service\supplier;
use app\service\Base;

class Url extends Base
{
	protected $_model = 'app/model/supplier/Url';

	public function addUrl($url, $priority=0)
	{
		$domain = $this->getDomain($url);
		$data = $this->getItemId($domain, $url);
		if (empty($data)) {
			return false;
		}
		$data['name'] = $domain;
		$where = ['name'=>$data['name'], 'item_id'=>$data['item_id']];
		if ($this->getCountData($where)) return true;
		$data['priority'] = $priority;
		return $this->insert($data);
	}

	protected function getDomain($url)
	{
		if (strpos($url, '1688.com') !== false) return '1688';
		if (strpos($url, 'taobao.com') !== false) return 'taobao';
		if (strpos($url, 'tmall.com') !== false) return 'tmall';
		return '';
	}

	protected function getItemId($domain, $url)
	{
		$itemId = '';
		switch ($domain) {
			case '1688':
				$reg = '/^https\:\/\/detail\.1688\.com\/offer\/(\d+)\.html(?:.)*/i';
				preg_match_all($reg, $url, $res);
				if (!empty($res[1][0])) {
					$itemId = $res[1][0];
					$url = 'https://detail.1688.com/offer/'.$itemId.'.html';
				}
				break;
			case 'taobao':
				parse_str(parse_url($url)['query'] ?? '', $param);
				if (!empty($param['id'])) {
					$itemId = $param['id'];
					$url = 'https://item.taobao.com/item.htm?id='.$itemId;
				}
				break;
			case 'tmall':
				parse_str(parse_url($url)['query'] ?? '', $param);
				if (!empty($param['id'])) {
					$itemId = $param['id'];
					$url = 'https://detail.tmall.com/item.htm?id='.$itemId;
				}
				break;
		}
		if (empty($itemId)) {
			return false;
		}
		return [
			'item_id' => $itemId,
			'url' => $url,
		];
	}
}