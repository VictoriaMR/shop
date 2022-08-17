<?php 

namespace app\service\site;
use app\service\Base;

class Site extends Base
{
	const CACHE_KEY = 'site:';

	protected function getModel()
	{
		$this->baseModel = make('app/model/site/Site');
	}

	public function getInfoCache($key, $type='site_id')
	{
		$cacheKey = $this->getCacheKey($type, $key);
		$info = redis(2)->get($cacheKey);
		if (!$info) {
			$info = $this->getInfo($key, $type);
			redis(2)->set($cacheKey, $info);
		}
		return $info;
	}

	protected function getCacheKey($type, $key)
	{
		return self::CACHE_KEY.$type.':'.$key;
	}

	public function getInfo($key, $type='site_id')
	{
		return $this->loadData([$type=>$key, 'status'=>1], 'site_id,type,path,name,domain,cate_id,view_suffix,email,cache');
	}

	public function deleteTemplateCache($siteId=0)
	{
		$where = [];
		if ($siteId > 0) {
			$where['site_id'] = $siteId;
		} else {
			$where['site_id'] = ['>=', 80];
		}
		$list = $this->getListData($where, 'path');
		foreach ($list as $value) {
			$dir = ROOT_PATH.'template'.DS.$value['path'].DS.'cache';
			if (is_dir($dir)) {
				$this->deleteDir($dir);
			}
		}
		return true;
	}

	private function deleteDir($dir)
	{
	    if (!$handle = @opendir($dir)) {
	        return false;
	    }
	    while (false !== ($file = readdir($handle))) {
	        if ($file !== "." && $file !== "..") {
	            $file = $dir . '/' . $file;
	            if (is_dir($file)) {
	                $this->deleteDir($file);
	            } else {
	                @unlink($file);
	            }
	        }

	    }
	    @rmdir($dir);
	}
}