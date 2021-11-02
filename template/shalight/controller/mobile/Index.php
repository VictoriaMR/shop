<?php

namespace template\shalight\controller\mobile;
use app\controller\Base;

class Index extends Base
{
	public function index()
	{
		html()->addJs('slider');
		$banner = [];
		for ($i=1;$i<6;$i++) {
			$banner[] = [
				'title' => '',
				'image' => siteUrl('image/mobile/banner/banner'.$i.'.jpg'),
				'url'=> 'https://www.baidu.com',
			];
		}
		//获取热门分类
		$cateList = make('app/service/site/CategoryUsed')->getListData(['site_id'=>siteId()], 'cate_id,attach_id', 1, 24, ['sort'=>'desc']);
		$allCate = array_column($cateList, 'cate_id');
		//获取分类语言
		$cateLanguage = [];
		if (lanId() != 'en') {
			$cateLanguage = make('app/service/category/Language')->getListData(['cate_id'=>['in', $allCate], 'lan_id'=>lanId()], 'cate_id,name');
			$cateLanguage = array_column($cateLanguage, 'name', 'cate_id');
		}
		$allCate = make('app/service/category/Category')->getListData(['cate_id'=>['in', $allCate], 'status'=>1], 'cate_id,name,attach_id');
		$allCate = array_column($allCate, null, 'cate_id');
		$attachArr = array_filter(array_merge(array_column($cateList, 'attach_id', 'cate_id'), array_column($allCate, 'attach_id')));

		if (!empty($attachArr)) {
			$attachArr = make('app/service/attachment/Attachment')->getList(['attach_id'=>['in', array_unique($attachArr)]]);
			$attachArr = array_column($attachArr, null, 'attach_id');
		}
		foreach ($cateList as $key=>$value) {
			$nameEn = empty($allCate[$value['cate_id']]['name']) ? '' : $allCate[$value['cate_id']]['name'];
			$value['name'] = $cateLanguage[$value['cate_id']] ?? $nameEn;
			$value['image'] = $value['attach_id'] ? $attachArr[$value['attach_id']]['url'] : (empty($allCate[$value['cate_id']]['attach_id']) ? '' : $attachArr[$allCate[$value['cate_id']]['attach_id']]['url']);
			$value['url'] = router()->urlFormat($nameEn, 'category', ['id'=>$value['cate_id']]);
			$cateList[$key] = $value;
		}
		$cateLeftList = array_slice($cateList, 0, 4);
		$cateRightList = array_slice($cateList, 4, 2);
		$cateList = array_chunk(array_slice($cateList, 6), 2);

		$this->assign('cateList', $cateList);
		$this->assign('cateLeftList', $cateLeftList);
		$this->assign('cateRightList', $cateRightList);
		$this->assign('banner', $banner);
	}
}