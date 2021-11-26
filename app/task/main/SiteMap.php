<?php

namespace app\task\main;
use app\task\TaskDriver;

class SiteMap extends TaskDriver
{
	private $siteInfo = [];
	private $msg = ""; //信息
	private $maxXml = 10; //xml文件记录url最大条数
	private $xmlDom = null; //xml实例
	private $xmlRoot = null;//根节点
	private $xmlFile = []; //xml文件列表
	private $siteId = null; //当前站点
	private $lastSpuId = null; //最后一个完成文件中spuid
	private $xmlCount = 0; //站点xml文件条数
	const SITEMAP_CACHE_KEY = 'sitemap-last-change';

	public function __construct($process=[])
	{
		parent::__construct($process);
		if ($process !== false) {
			$this->lockTimeout = config('task.timeout');
			// 每运行6小时退出一次
			$this->runTimeLimit = 60*60*6;
		}
		$this->config['info'] = '站点地图任务';
		$this->config['cron'] = ['1 14 * * 00']; //每周1次
	}

	public function run()
	{
		$siteList = make('app/service/site/Site')->getListData(['site_id'=>['>=', 80]], 'site_id,path,domain');
		$cateUsedService = make('app/service/site/CategoryUsed');
		$cateLanguageService = make('app/service/category/Language');
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
						'loc' => $router->urlFormat($cateNameArr[$cv]??'', 'c', ['id'=>$cv]),
					];
					$this->createSingleXml('url', $tempData);
					//条数统计
				}
				$this->checkXml();
				dd($cateNameArr);
			}

		}
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
		return redis(3)->hGet(self::SITEMAP_CACHE_KEY, $siteId);
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
			mkdir($filePath, 0777, true);
		}
		//自动命名
		if (empty($name)) {
			$count = count($this->xmlFile);
			// 临时文件名称
			$name = sprintf('%s_%d%s.xml', 'sitemap', ++$count,'_temp');
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
}