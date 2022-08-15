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

		$bannerPath = ROOT_PATH.'template'.DS.APP_TEMPLATE_PATH.DS;
		$arr = getDirFile($bannerPath.'image'.DS.'computer'.DS.'banner');
		$url = [
			url('daily-pants-c', ['id'=>132]),
			url('sportswear-c', ['id'=>101]),
			url('daily-dress-c', ['id'=>104])
		];
		$banner = [];
		foreach ($arr as $key=>$value) {
			$banner[] = [
				'url' => $url[$key],
				'image' => siteUrl(str_replace($bannerPath, '', $value)),
			];
		}
		$cateArr = make('app/service/category/Category')->getListFormat();
		$tempArr = [];
		$cateId = 0;
		foreach ($cateArr as $value) {
			if ($value['parent_id'] == 0) {
				$cateId = $value['cate_id'];
				$tempArr[$cateId] = [];
			}
			$tempArr[$cateId][] = $value;
		}
		$cateArr = $tempArr[\App::get('base_info', 'cate_id')] ?? [];
		$this->assign('banner', $banner);
		$this->assign('cateArr', $cateArr);
	}
}