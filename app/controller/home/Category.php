<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Category extends HomeBase
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();
		$cateId = iget('id', 0);
		
		$category = make('app/service/category/Category');
		$cateInfo = $category->getSiteInfoCache($cateId);
		$crumbs[] = [
			'name' => 'Home',
			'url' => url(),
		];
		if ($cateInfo) {
			$crumbs[] = [
				'name' => $cateInfo['name_en'],
				'url' => url($cateInfo['name_en'].'-c', ['id'=>$cateInfo['cate_id']]),
			];
			$cateSon = $category->getSubCategoryById($cateId);
			foreach ($cateSon as $key=>$value) {
				$info = $category->getSiteInfoCache($value);
				if ($info) {
					$cateSon[$key] = $info;
				} else {
					unset($cateSon[$key]);
				}
			}
			$this->assign('cateSon', $cateSon);
		}
		$this->assign('cateInfo', $cateInfo);
		$this->assign('crumbs', $crumbs);
		$this->view(true);
	}
}