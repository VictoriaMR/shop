<?php 

namespace app\service\supplier;
use app\service\Base;

class Url extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/supplier/Url');
	}

	public function addUrl($url)
	{
		$domain = $this->getDomain($url);
		$data = $this->getItemId($domain, $url);
		$data['name'] = $domain;
		$where = ['name'=>$data['name'], 'item_id'=>$data['item_id']];
		if ($this->getCountData($where)) return true;
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
		return [
			'item_id' => empty($itemId) ? md5($url) : $itemId,
			'url' => $url,
		];
	}
}