<?php 

namespace app\service\site;
use app\service\Base;

class CategoryUsed extends Base
{
	const CACHE_KEY = 'site-category-used:';

	public function getList($where=[], $page=1, $size=30)
	{
		$list = $this->getListData($where, '*', $page, $size, ['sort'=>'asc']);
		$cateService = service('category/Category');
		//图片
		$attachArr = array_filter(array_column($list, 'attach_id'));
		if (!empty($attachArr)) {
			$attachArr = service('attachment/Attachment')->getList(['attach_id'=>['in', $attachArr]], 200);
			$attachArr = array_column($attachArr, 'url', 'attach_id');
		}
		foreach ($list as $key => $value) {
			$value['parent'] = array_reverse($cateService->pCate($value['cate_id'], false));
			$value['avatar'] = $attachArr[$value['attach_id']] ?? '';
			$list[$key] = $value;
		}
		return $list;
	}

	public function getListCache($siteId=null)
	{
		if (is_null($siteId)) {
			$siteId = $this->siteId();
		}
		$cacheKey = $this->getCacheKey($siteId);
		$list = redis()->get($cacheKey);
		if (false === $list) {
			$list = $this->getList($siteId);
			redis()->set($cacheKey, $list);
		}
		return $list;
	}

	public function delCache($siteId)
	{
		return redis()->del($this->getCacheKey($siteId));
	}

	protected function getCacheKey($siteId)
	{
		return self::CACHE_KEY.(is_null($siteId) ? $this->siteId() : $siteId);
	}
}