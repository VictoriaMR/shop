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
		foreach ($cateArr as $value) {
			if ($value['parent_id'] == 0) {
				$cateId = $value['cate_id'];
				$tempArr[$cateId] = [];
			}
			$tempArr[$cateId][] = $value;
			if ($value['is_hot']) {
				$hotArr[] = $value;
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

		$this->assign('banner', $banner);
		$this->assign('cateArr', $cateArr);
		$this->assign('hotArr', $hotArr);
	}
}