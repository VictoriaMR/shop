<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Category extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '品类管理',
			'siteCategory' => '站点品类使用',
		];
		$this->_default = '品类管理';
	}

	public function index()
	{	
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getCateInfo', 'getCateLanguage', 'editInfo', 'editLanguage', 'sortCategory', 'deleteCategory', 'transfer', 'modifyCategory'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		html()->addJs();

		$list = make('app/service/category/Category')->getListFormat();
		if (!empty($list)) {
			$cateArr = array_column($list, 'cate_id');
			$cateArr = make('app/service/category/Language')->where(['cate_id'=>['in', $cateArr]])->field('count(*) as count, cate_id')->groupBy('cate_id')->get();
			$cateArr = array_column($cateArr, 'count', 'cate_id');
			$languageList = make('app/service/Language')->getListData();
			$languageList = array_column($languageList, null, 'lan_id');
			unset($languageList[1]);
			$len = count($languageList);
			//图片
			$attachArr = array_filter(array_column($list, 'attach_id'));
			if (!empty($attachArr)) {
				$attachArr = make('app/service/attachment/Attachment')->getList(['attach_id'=>['in', $attachArr]]);
				$attachArr = array_column($attachArr, 'url', 'attach_id');
			}
			foreach ($list as $key => $value) {
				$value['is_translate'] = empty($cateArr[$value['cate_id']]) ? 0 : ($cateArr[$value['cate_id']] < $len ? 1 : 2);
				$value['avatar'] = $attachArr[$value['attach_id']] ?? '';
				$list[$key] = $value;
			}
		}
		
		$this->_init();
		$this->assign('list', $list);
		$this->view();
	}

	protected function getCateInfo()
	{
		$cateId = (int)ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/category/Category')->loadData($cateId);
		$this->success($info);
	}

	protected function getCateLanguage()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/category/Language')->getListData(['cate_id'=>$cateId]);
		$info = array_column($info, 'name', 'lan_id');
		$languageList = make('app/service/Language')->getListCache();
		$data = [];
		foreach ($languageList as $key => $value) {
			if ($value['lan_id'] == 1) continue;
			$data[] = [
				'lan_id' => $value['lan_id'],
				'tr_code' => $value['tr_code'],
				'name' => $info[$value['lan_id']] ?? '',
				'language_name' => $value['name2'],
			];
		}
		$this->success($data);
	}

	protected function editLanguage()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$language = ipost('language');
		if (!empty($language)) {
			$services = make('app/service/category/Language');
			foreach ($language as $key => $value) {
				$services->setNxLanguage($cateId, $key, strTrim($value));
			}
		}
		$this->addLog('修改分类语言-'.$cateId);
		$this->success('操作成功');
	}

	protected function editInfo()
	{
		$cateId = (int) ipost('cate_id');
		$parentId = (int) ipost('parent_id');
		$name = trim(ipost('name'));
		$name_en = trim(ipost('name_en', ''));
		if (empty($name)) {
			$this->error('名称不能为空');
		}
		$data = [
			'parent_id' => $parentId,
			'name' => $name,
			'name_en' => $name_en,
		];
		if (empty($cateId)) {
			$result = make('app/service/category/Category')->insert($data);
			$this->addLog('新增分类-'.$result);
		} else {
			$this->addLog('修改分类信息-'.$cateId);
			$result = make('app/service/category/Category')->updateData($cateId, $data);
		}
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function deleteCategory()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$services = make('app/service/category/Category');
		if ($services->hasChildren($cateId)) {
			$this->error('该分类有子分类, 不能删除');
		}
		if ($services->hasProduct($cateId)) {
			$this->error('该分类有产品, 不能删除');
		}
		$result = $services->deleteDataById($cateId);
		if ($result) {
			$this->addLog('删除分类语言-'.$cateId);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}

	protected function modifyCategory()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$attachId = ipost('attach_id', -1);
		$status = ipost('status', -1);
		$show = ipost('show', -1);
		$data = [];
		if ($attachId >= 0) {
			$data['attach_id'] = $attachId;
		}
		if ($status >= 0) {
			$data ['status'] = $status;
		}
		if ($show >= 0) {
			$data['show'] = $show;
		}
		$categoryService = make('app/service/category/Category');
		$rst = $categoryService->updateData($id, $data);
		if ($rst) {
			if ($status == 0 && $categoryService->hasChildren($id)) {
				$categoryService->updateData(['parent_id'=>$id], ['status'=>$status]);
			}
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	public function siteCategory()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['editSiteCategory', 'deleteSiteCategory', 'modifySiteCategory'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		html()->addJs();
		$site = iget('site', -1);
		$cate = iget('cate', -1);
		$page = iget('page', 1);
		$size = iget('size', 30);
		//站点列表
		$siteList = make('app/service/site/Site')->getListData([], 'site_id,name');
		$siteList = array_column($siteList, 'name', 'site_id');
		//分类列表
		$cateList = make('app/service/category/Category')->getListFormat();
		$where = [];
		if ($site > 0) {
			$where['site_id'] = $site;
		}
		if ($cate > 0) {
			$where['cate_id'] = $cate;
		}
		$cateUsedService = make('app/service/site/CategoryUsed');
		$total = $cateUsedService->getCountData($where);
		if ($total > 0) {
			$list = $cateUsedService->getList($where, $page, $size);
		}
		$this->assign('total', $total);
		$this->assign('list', $list ?? []);
		$this->assign('size', $size);
		$this->assign('site', $site);
		$this->assign('cate', $cate);
		$this->assign('siteList', $siteList);
		$this->assign('cateList', $cateList);
		$this->_init();
		$this->view();
	}

	protected function editSiteCategory()
	{
		$id = ipost('item_id');
		$siteId = ipost('site_id');
		$cateId = ipost('cate_id');
		if (empty($siteId) || empty($cateId)) {
			$this->error('参数不正确');
		}
		$cateArr = make('app/service/category/Category')->getSubCategoryById($cateId);
		$cateUsedService = make('app/service/site/CategoryUsed');
		//获取已有的ID
		$hasArr = $cateUsedService->getListData(['site_id'=>$siteId, 'cate_id'=>['in', $cateArr]], 'cate_id');
		if (!empty($hasArr)) {
			$cateArr = array_diff($cateArr, array_column($hasArr, 'cate_id'));
		}
		if (empty($cateArr)) {
			$this->error('所有分类已存在');
		}
		$insert = [];
		foreach($cateArr as $value) {
			$insert[] = [
				'site_id' => $siteId,
				'cate_id' => $value,
			];
		}
		$cateUsedService->insert($insert);
		$this->success('添加分类成功');
	}

	protected function deleteSiteCategory()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$cateUsedService = make('app/service/site/CategoryUsed');
		$info = $cateUsedService->loadData($id, 'site_id,cate_id');
		if (empty($info)) {
			$this->error('数据不存在');
		}
		if (make('app/service/product/Spu')->getCountData($info)) {
			$this->error('该分类下有产品, 不能删除');
		}
		$rst = $cateUsedService->deleteData($id);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function modifySiteCategory()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$attachId = ipost('attach_id');
		$sort = ipost('sort');
		$saleTotal = ipost('sale_total');
		$visitTotal = ipost('visit_total');
		$data = [];
		if (!empty($attachId)) {
			$data['attach_id'] = $attachId;
		}
		if (!is_null($sort)) {
			$data['sort'] = $sort;
		}
		if (!is_null($saleTotal)) {
			$data['sale_total'] = $saleTotal;
		}
		if (!is_null($visitTotal)) {
			$data['visit_total'] = $visitTotal;
		}
		if (empty($data)) {
			$this->error('参数不正确');
		}
		$rst = make('app/service/site/CategoryUsed')->updateData($id, $data);
		if ($rst) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}
}