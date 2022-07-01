<?php

namespace app\task\main;
use app\task\TaskDriver;

class SiteMap extends TaskDriver
{
	private $siteInfo = [];
	private $msg = ""; //信息
	private $maxXml = 4900; //xml文件记录url最大条数
	private $xmlDom = null; //xml实例
	private $xmlRoot = null;//根节点
	private $xmlFile = []; //xml文件列表
	private $siteId = null; //当前站点
	private $lastSpuId = null; //最后一个完成文件中spuid
	private $xmlCount = 0; //站点xml文件条数
	const SITEMAP_CACHE_KEY = 'sitemap-last-change';

	public $config = [
        'info' => '站点地图任务',
        'cron' => ['1 14 * * 00'],
    ];

	public function run()
	{
		$siteList = make('app/service/site/Site')->getListData(['site_id'=>['>=', 80]], 'site_id,path,domain');
		$cateUsedService = make('app/service/site/CategoryUsed');
		$cateLanguageService = make('app/service/category/Language');
		$spuService = make('app/service/product/Spu');
		$spuLanguageService = make('app/service/product/Language');
		$skuService = make('app/service/product/Sku');
		$attrUsedService = make('app/service/product/AttrUsed');
		$attvLanguageService = make('app/service/attr/ValueLanguage');
		$router = make('frame/Router');
		foreach ($siteList as $key => $value) {
			$this->clear();
			$this->lastSpuId = $this->getLastSpuId($value['site_id']);
			//astid 不为空, 首页和分类已经遍历
			if (!$this->createXml('urlset')) {
				return false;
			}
			$this->siteInfo = $value;
			if (empty($this->lastSpuId)) {
				$tempData = [
					'loc' => $value['domain'],
				];
				$this->createSingleXml('url', $tempData);
				//获取分类信息
				$cateIdArr = $cateUsedService->getListData(['site_id'=>$value['site_id']], 'cate_id');
				if (empty($cateIdArr)) continue;
				$cateIdArr = array_column($cateIdArr, 'cate_id');
				$cateNameArr = $cateLanguageService->getListData(['cate_id'=>['in', $cateIdArr], 'lan_id'=>'en'], 'cate_id,name');
				$cateNameArr = array_column($cateNameArr, 'name', 'site_id');
				foreach ($cateIdArr as $cv) {
					$tempData = [
						'loc' => $router->urlFormat($cateNameArr[$cv]??'', 'c', ['id'=>$cv], $value['domain']),
					];
					$this->createSingleXml('url', $tempData);
					//条数统计
					$this->checkXml();
				}
			}
			//站点产品
			$where = ['site_id'=>$value['site_id'], 'status'=>1, 'spu_id'=>['>', $this->lastSpuId]];
			$total = $spuService->getCountData($where);
			if ($total > 0) {
				$size = 100;
				for ($i=1; $i <= ceil($total / $size); $i++) {
					$spuIdArr = $spuService->getListData($where, 'spu_id', $i, $size);
					$spuIdArr = array_column($spuIdArr, 'spu_id');
					//获取名称
					$spuNameArr = $spuLanguageService->getListData(['spu_id'=>['in', $spuIdArr], 'lan_id'=>'en'], 'spu_id,name');
					$spuNameArr = array_column($spuNameArr, 'name', 'spu_id');
					$skuList = $skuService->getListData(['spu_id'=>['in', $spuIdArr], 'status'=>1], 'sku_id,spu_id');
					$tempData = [];
					$skuIdArr = array_column($skuList, 'sku_id');
					$attvArr = $attrUsedService->getListData(['sku_id'=>['in', $skuIdArr]], 'sku_id,attv_id', 0, 0, ['sku_id'=>'asc', 'sort'=>'asc']);
					$attvIdArr = array_unique(array_column($attvArr, 'attv_id'));
					foreach ($attvArr as $attrValue) {
						$tempData[$attrValue['sku_id']][] = $attrValue['attv_id'];
					}
					$attvArr = $tempData;
					//获取名称
					$attvNameArr = $attvLanguageService->getListData(['attv_id'=>['in', $attvIdArr], 'lan_id'=>'en'], 'attv_id,name');
					$attvNameArr = array_column($attvNameArr, 'name', 'attv_id');
					$tempData = [];
					foreach ($skuList as $skuValue) {
						$tempData[$skuValue['spu_id']][] = $skuValue['sku_id'];
					}
					$skuList = $tempData;
					foreach ($spuIdArr as $spuValue) {
						$spuNameArr[$spuValue] = empty($spuNameArr[$spuValue])?'':$spuNameArr[$spuValue];
						$tempData = [
							'loc' => $router->urlFormat($spuNameArr[$spuValue], 'p', ['id'=>$spuValue], $value['domain']),
						];
						$this->lastSpuId = $spuValue;
						$this->createSingleXml('url', $tempData);
						if (!empty($skuList[$spuValue])) {
							foreach ($skuList[$spuValue] as $skuValue) {
								$str = '';
								foreach ($attvArr[$skuValue] as $attvValue) {
									$str .= '-'.($attvNameArr[$attvValue] ?? '');
								}
								$name = $spuNameArr[$spuValue].$str;
								$tempData = [
									'loc' => $router->urlFormat($spuNameArr[$spuValue], 's', ['id'=>$skuValue], $value['domain']),
								];
								$this->createSingleXml('url', $tempData);
							}
						}
						//条数统计
						$this->checkXml();
					}
				}
			}
			if ($this->xmlCount > 0) {
				$this->saveSingleXml();
			}
			$this->saveSiteMapIndexXml($value['domain']);
		}
		$this->nextRunAt();
		return true;
	}

	protected function clear()
	{
		$this->xmlDom = null;
		$this->xmlDom = null;
		$this->xmlRoot = null;
		$this->xmlFile = [];
		$this->siteId = null;
		$this->xmlCount = 0;
		$this->lastSpuId = 0;
		return true;
	}

	protected function getLastSpuId($siteId)
	{
		return (int)redis(3)->hGet(self::SITEMAP_CACHE_KEY, $siteId);
	}

	protected function createXml($name)
	{
		try {
			//创建一个XML文档并设置XML版本和编码
			$this->xmlDom = new \DomDocument('1.0', 'utf-8');
			//创建根节点
			$this->xmlRoot = $this->xmlDom->createElement($name);
			$xmlns = $this->xmlDom->createAttribute('xmlns');
			$xmlnsUrl = $this->xmlDom->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
			$xmlns->appendChild($xmlnsUrl);
			//插入协议
			return $this->xmlRoot->appendchild($xmlns);
		} catch (\Exception $e) {
			return false;
		}
	}

	protected function createSingleXml($type, $data)
	{
		if (is_null($this->xmlDom)) return false;
		if (empty($data) || !is_array($data)) return false;
		$type = $this->xmlDom->createElement($type);
		if (!$type) return false;
		//转义字符
		$trans = [
			'&' => '&amp;',
			"'" => '&apos;',
			'"' => '&quot;',
			'>' => '&gt;',
			'<' => '&lt;',
		];
		foreach ($data as $key => $value) {
			//网址实体转义
			if ($key == 'loc') $value = strtr($value, $trans);;
			$temp = $this->xmlDom->createElement($key, $value);
			$res = $type->appendchild($temp);
			if (!$res) return false;
		}
		//插入根节点中
		$res = $this->xmlRoot->appendchild($type);
		$this->xmlCount++;
		return $res;
	}

	protected function checkXml()
	{
		if ($this->xmlCount < $this->maxXml) return true;
		$this->xmlCount = 0;
		// 保存文件 并重新生成实例
		$this->saveSingleXml();
		return $this->createXml('urlset');
	}

	protected function saveSingleXml($name = '')
	{
		if (is_null($this->xmlDom)) return false;
		$isIndexXml = empty($name);
		//站点路径
		$filePath = $this->getPath();
		if (empty($filePath)) return false;
		if (!is_dir($filePath)) {
			mkdir($filePath, 0755, true);
		}
		//自动命名
		if (empty($name)) {
			$count = count($this->xmlFile);
			// 临时文件名称
			$name = sprintf('%s_%d.xml', 'sitemap', ++$count);
			//根节点
			$this->xmlFile[] = $name;
		}
		$saveFile = $filePath.$name;
		$this->xmlDom->appendchild($this->xmlRoot);
		return $this->xmlDom->save($saveFile);
	}

	protected function getPath()
	{
		return ROOT_PATH.'template'.DS.$this->siteInfo['path'].DS.'sitemap'.DS;
	}

	protected function saveSiteMapIndexXml($domain)
	{
		if (!$this->createXml('sitemapindex')) {
			return false;
		}
		$path = $this->getPath();
		$tempXmlCount = 0;
		foreach ($this->xmlFile as $key => $value) {
			if (!is_file($path.$value)) continue;
			$url = $domain.$value;
			$tempData = [
				'loc' => $url,
			];
			$res = $this->createSingleXml('sitemap', $tempData);
			if ($res) {
				$tempXmlCount ++;
			}
		}
		if ($tempXmlCount < 1) {
			return false;
		}
		return $this->saveSingleXml('sitemap.xml');
	}
}