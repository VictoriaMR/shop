<?php 

namespace app\service\site;
use app\service\Base;

class CategoryUsed extends Base
{
	const CACHE_KEY = 'site-category-used:';

	protected function getModel()
	{
		$this->baseModel = make('app/model/site/CategoryUsed');
	}

	public function getList($where=[], $page=1, $size=30)
	{
		$list = $this->getListData($where, '*', $page, $size, ['sort'=>'asc']);
		$cateService = make('app/service/category/Category');
		$allCateArr = $cateService->getListData();
		$allCateArr = array_column($allCateArr, null, 'cate_id');
		//图片
		$attachArr = array_filter(array_column($list, 'attach_id'));
		if (!empty($attachArr)) {
			$attachArr = make('app/service/Attachment')->getList(['attach_id'=>['in', $attachArr]], 200);
			$attachArr = array_column($attachArr, 'url', 'attach_id');
		}
		foreach ($list as $key => $value) {
			$value['parent'] = array_reverse($cateService->getParentCategoryById($allCateArr, $value['cate_id']));
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