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
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, [''])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addJs();

		$this->view([
			'list' => service('category/Category')->getListData(['parent_id'=>0])
		]);
	}

	public function cateList()
	{	
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getCateInfo', 'getCateLanguage', 'editInfo', 'editLanguage', 'sortCategory', 'deleteCategory', 'transfer', 'modifyCategory'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();
		$cid = iget('cid', 0);
		$pList = [];
		$list = [];
		$tempList = service('category/Category')->getListFormat(false);
		if (!empty($tempList)) {
			$count = 0;
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
				$cateArr = service('category/Language')->where(['cate_id'=>['in', $cateArr]])->field('count(*) as count, cate_id')->groupBy('cate_id')->get();
				$cateArr = array_column($cateArr, 'count', 'cate_id');
				$languageList = sys()->language()->getListData();
				$languageList = array_column($languageList, null, 'lan_id');
				unset($languageList[1]);
				$len = count($languageList);
				//图片
				$attachArr = array_filter(array_column($list, 'attach_id'));
				if (!empty($attachArr)) {
					$attachArr = service('attachment/Attachment')->getList(['attach_id'=>['in', $attachArr]]);
					$attachArr = array_column($attachArr, 'url', 'attach_id');
				}
				$groupCount = [];
				foreach ($list as $value) {
					$pid = $value['parent_id'];
					$groupCount[$pid] = ($groupCount[$pid] ?? 0) + 1;
				}
				$groupIndex = [];
				foreach ($list as $key => $value) {
					$pid = $value['parent_id'];
					$groupIndex[$pid] = ($groupIndex[$pid] ?? 0) + 1;
					$value['is_first'] = ($groupIndex[$pid] === 1);
					$value['is_last'] = ($groupIndex[$pid] === $groupCount[$pid]);
					$value['is_translate'] = empty($cateArr[$value['cate_id']]) ? 0 : ($cateArr[$value['cate_id']] < $len ? 1 : 2);
					$value['avatar'] = $attachArr[$value['attach_id']] ?? '';
					$list[$key] = $value;
				}
			}
		}

		$this->view([
			'cid' => $cid,
			'pList' => $pList,
			'list' => $list,
		]);
	}

	protected function getCateInfo()
	{
		$cateId = (int)ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = service('category/Category')->loadData($cateId);
		if (!empty($info['parent_id'])) {
			$parent = service('category/Category')->loadData($info['parent_id']);
			$info['parent_name'] = $parent['name'] ?? '';
		} else {
			$info['parent_name'] = '顶级分类';
		}
		$this->success('获取成功', $info);
	}

	protected function getCateLanguage()
	{
		$cateId = (int) ipost('cate_id');
		$type = (int) ipost('type', 0);
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = service('category/Language')->getListData(['cate_id'=>$cateId, 'type'=>$type]);
		$info = array_column($info, 'name', 'lan_id');
		$languageList = sys()->language()->getListData();
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
		$this->success('获取成功', $data);
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
			$services = service('category/Language');
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
			$result = service('category/Category')->insert($data);
			$this->addLog('新增分类-'.$result);
		} else {
			$this->addLog('修改分类信息-'.$cateId);
			$result = service('category/Category')->updateData($cateId, $data);
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
		$services = service('category/Category');
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
		$categoryService = service('category/Category');
		$rst = $categoryService->updateData($id, $data);
		if ($rst) {
			if ($status == 0 && $categoryService->hasChildren($id)) {
				$subCates = $categoryService->sCate($id, false);
				if (!empty($subCates)) {
					$subIds = array_column($subCates, 'cate_id');
					$categoryService->updateData(['cate_id' => ['in', $subIds]], ['status' => 0]);
				}
			}
			if ($isShow == 0 && $categoryService->hasChildren($id)) {
				$subCates = $categoryService->sCate($id, false);
				if (!empty($subCates)) {
					$subIds = array_column($subCates, 'cate_id');
					$categoryService->updateData(['cate_id' => ['in', $subIds]], ['is_show' => 0]);
				}
			}
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function sortCategory()
	{
		$cateId = (int) ipost('cate_id');
		$type = trim(ipost('type', ''));
		$parentId = (int) ipost('parent_id', 0);

		if (!empty($cateId) && !empty($type)) {
			$result = service('tool/Sort')->sort('category', $cateId, $type, 'cate_id', 'sort', ['parent_id' => $parentId]);
			if ($result) {
				redis()->del('category:list-cache');
				$this->addLog('分类排序-' . $cateId . '-' . $type);
				$this->success('排序成功');
			} else {
				$this->error('排序失败');
			}
		}

		$data = ipost('data');
		if (!empty($data) && is_array($data)) {
			$categoryService = service('category/Category');
			foreach ($data as $pidKey => $items) {
				if (is_array($items)) {
					foreach ($items as $sortIndex => $id) {
						$categoryService->updateData((int)$id, ['sort' => $sortIndex + 1]);
					}
				}
			}
			redis()->del('category:list-cache');
			$this->addLog('批量更新分类排序');
			$this->success('排序成功');
		}

		$this->error('参数不正确');
	}

	public function attrUsed()
	{
		$this->view();
	}
}