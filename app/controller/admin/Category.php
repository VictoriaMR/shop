<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Category extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '品类管理',
			'cateList' => '子类目管理',
			'attrUsed' => '属性映射',
		];
		$this->_ignore = ['attrUsed'];
		$this->_default = '产品分类';
		parent::_init();
	}

	public function index()
	{	
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, [''])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		html()->addJs();
		$cid = iget('cid', 0);
		$tempList = make('app/service/category/Category')->getListFormat(false);
		if (!empty($tempList)) {
			$list = [];
			foreach ($tempList as $value) {
				if ($value['level'] == 0) {
					$list[] = $value;
				}
			}
		}
		
		$this->assign('list', $list ?? []);
		$this->view();
	}

	public function cateList()
	{	
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getCateInfo', 'getCateLanguage', 'editInfo', 'editLanguage', 'sortCategory', 'deleteCategory', 'transfer', 'modifyCategory'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		html()->addJs();
		$cid = iget('cid', 0);
		$tempList = make('app/service/category/Category')->getListFormat(false);
		if (!empty($tempList)) {
			$count = 0;
			$pList = [];
			$list = [];
			$status = false;
			foreach ($tempList as $value) {
				if ($value['level'] == 0) {
					if (!$cid) {
						$cid = $value['cate_id'];
					}
					if (isset($pList[count($pList)-1])) {
						$pList[count($pList)-1]['count'] = $count;
					}
					$count = 0;
					$pList[] = $value;
					if ($cid == $value['cate_id']) {
						$status = true;
					} else {
						$status = false;
					}
				} else {
					$count++;
					if ($status) {
						$list[] = $value;
					}
				}
			}
			if (!empty($pList)) {
				$pList[count($pList)-1]['count'] = $count;
			}
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
		}
		
		$this->assign('cid', $cid);
		$this->assign('pList', $pList ?? []);
		$this->assign('list', $list ?? []);
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
		$type = (int) ipost('type', 0);
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/category/Language')->getListData(['cate_id'=>$cateId, 'type'=>$type]);
		$info = array_column($info, 'name', 'lan_id');
		$languageList = make('app/service/Language')->getListData();
		$data = [];
		foreach ($languageList as $key => $value) {
			if ($value['lan_id'] <= 1 && !$type) continue;
			$data[] = [
				'lan_id' => $value['lan_id'],
				'tr_code' => $value['tr_code'],
				'name' => $info[$value['lan_id']] ?? '',
				'language_name' => $value['name'],
			];
		}
		$this->success($data);
	}

	protected function editLanguage()
	{
		$cateId = (int) ipost('cate_id');
		$type = (int) ipost('type', 0);
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$language = ipost('language');
		if (!empty($language)) {
			$services = make('app/service/category/Language');
			foreach ($language as $key => $value) {
				$services->setNxLanguage($cateId, $key, $type, strTrim($value));
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
		$isShow = ipost('is_show', -1);
		$isHot = ipost('is_hot', -1);
		$data = [];
		if ($attachId >= 0) {
			$data['attach_id'] = $attachId;
		}
		if ($status >= 0) {
			$data ['status'] = $status;
		}
		if ($isShow >= 0) {
			$data['is_show'] = $isShow;
		}
		if ($isHot >= 0) {
			$data['is_hot'] = $isHot;
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

	public function attrUsed()
	{
		$this->view();
	}
}