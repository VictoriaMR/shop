<?php

namespace template\newsme\controller\computer;
use app\controller\Base;

class Index extends Base
{
	public function index()
	{
		html()->addCss('slider');
		html()->addCss('clothes-icon');
		html()->addJs('slider');

		$cateArr = make('app/service/category/Category')->getListFormat();
		$tempArr = [];
		$cateId = 0;
		$hotArr = [];
		$popularCate = [];
		foreach ($cateArr as $value) {
			if ($value['parent_id'] == 0) {
				$cateId = $value['cate_id'];
				$tempArr[$cateId] = [];
			}
			$tempArr[$cateId][] = $value;
			if ($value['is_hot']) {
				$hotArr[] = $value;
			}
			if ($value['attach_id']) {
				$popularCate[] = $value;
			}
		}
		$cateArr = $tempArr[\App::get('base_info', 'cate_id')] ?? [];

		$bannerPath = ROOT_PATH.'template'.DS.APP_TEMPLATE_PATH.DS;
		$arr = getDirFile($bannerPath.'image'.DS.'computer'.DS.'banner');
		$banner = [];
		foreach ($arr as $key=>$value) {
			if (!isset($hotArr[$key])) break;
			$banner[] = [
				'url' => url($hotArr[$key]['name_en'].'-c', ['id'=>$hotArr[$key]['cate_id']]),
				'image' => siteUrl(str_replace($bannerPath, '', $value)),
			];
		}
		if (!empty($popularCate)) {
			$attachArr = array_filter(array_column($popularCate, 'attach_id'));
			$attachArr = make('app/service/attachment/Attachment')->getList(['attach_id'=>['in', $attachArr]]);
			$attachArr = array_column($attachArr, 'url', 'attach_id');
			foreach ($popularCate as $key=>$value) {
				$popularCate[$key]['image'] = $attachArr[$value['attach_id']] ?? '';
			}
		}

		$bestSeller = make('app/service/product/Spu')->getRecommend();

		$this->assign('banner', $banner);
		$this->assign('cateArr', $cateArr);
		$this->assign('hotArr', $hotArr);
		$this->assign('popularCate', $popularCate);
		$this->assign('bestSeller', $bestSeller);
	}
}