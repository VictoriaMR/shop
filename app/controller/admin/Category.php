<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Category extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index'    => '品类管理',
			'cateList' => '子类目管理',
			'log'      => '分类日志',
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
			if (in_array($opn, ['getCateInfo', 'editInfo', 'deleteCategory', 'modifyCategory'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();

		$tempList = service('category/Category')->getListFormat(false);
		$list = [];
		if (!empty($tempList)) {
			$count = 0;
			foreach ($tempList as $value) {
				if ($value['level'] == 0) {
					if (!empty($list)) {
						$list[count($list) - 1]['count'] = $count;
					}
					$count = 0;
					$list[] = $value;
				} else {
					$count++;
				}
			}
			if (!empty($list)) {
				$list[count($list) - 1]['count'] = $count;
			}
		}

		$this->view([
			'list' => $list
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
			$oldInfo = $services->getListData(['cate_id' => $cateId, 'type' => $type]);
			$oldMap = array_column($oldInfo, 'name', 'lan_id');
			$langList = sys()->language()->getListData();
			$langMap = array_column($langList, 'name', 'lan_id');

			foreach ($language as $key => $value) {
				$services->setNxLanguage($cateId, $key, $type, strTrim($value));
			}

			$diff = service('log/Category')->diffLanguage($cateId, $type, $language, $oldMap, $langMap);
			service('log/Category')->addLog(\app\service\log\Category::TYPE_LANGUAGE, $cateId, '修改分类多语言配置 #' . $cateId, $diff);
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
		$logService = service('log/Category');
		$categoryService = service('category/Category');
		$type = \app\service\log\Category::TYPE_CATEGORY;

		if (empty($cateId)) {
			$result = $categoryService->insert($data);
			$logTitle = ($parentId == 0 ? '新增品类' : '新增子类目') . ' #' . $result;
			$detail = $logService->snapshotCategory($data);
			$this->addLog($logTitle);
			$logService->addLog($type, $result, $logTitle, $detail);
		} else {
			$oldInfo = $categoryService->loadData($cateId);
			$result = $categoryService->updateData($cateId, $data);
			$logTitle = ($parentId == 0 ? '修改品类' : '修改子类目') . ' #' . $cateId;
			$detail = $logService->diffCategory($oldInfo ?: [], $data);
			$this->addLog($logTitle);
			$logService->addLog($type, $cateId, $logTitle, $detail);
		}
		if ($result) {
			redis()->del('category:list-cache');
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
		$oldInfo = $services->loadData($cateId);
		$result = $services->deleteDataById($cateId);
		if ($result) {
			redis()->del('category:list-cache');
			$this->addLog('删除分类-'.$cateId);
			$type = \app\service\log\Category::TYPE_CATEGORY;
			$detail = service('log/Category')->snapshotCategory($oldInfo ?: ['name' => '分类-' . $cateId]);
			service('log/Category')->addLog($type, $cateId, '删除分类 #' . $cateId, $detail);
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
		$oldInfo = $categoryService->loadData($id);
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
			redis()->del('category:list-cache');
			$type = \app\service\log\Category::TYPE_CATEGORY;
			$detail = service('log/Category')->diffCategory($oldInfo ?: [], $data);
			service('log/Category')->addLog($type, $id, '修改分类属性 #' . $id, $detail);
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
				service('log/Category')->addLog(\app\service\log\Category::TYPE_SORT, $cateId, '分类排序调整 #' . $cateId, [
					['field' => 'sort', 'label' => '排序动作', 'old' => '-', 'new' => $type]
				]);
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

	protected function transfer()
	{
		$trCode = ipost('tr_code');
		$name = ipost('name');
		if (empty($name) || empty($trCode)) {
			$this->error('参数不正确');
		}
		$rst = service('tool/Translate')->getText($name, $trCode);
		if ($rst !== false && $rst !== null && $rst !== '') {
			$this->success('翻译成功', $rst);
		}
		$this->error('翻译失败');
	}

	public function attrUsed()
	{
		$this->view();
	}

	public function log()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$type = iget('type', -1);
		$cateId = iget('cate_id', '');
		$keyword = trim(iget('keyword', ''));
		$stime = trim(iget('stime', ''));
		$etime = trim(iget('etime', ''));
		$page = max(1, (int)iget('page', 1));
		$size = max(1, (int)iget('size', 20));

		$where = [];
		if ($type >= 0) {
			$where['type'] = (int)$type;
		}
		if ($cateId !== '') {
			$where['cate_id'] = (int)$cateId;
		}
		if ($keyword !== '') {
			$where['info'] = ['like', '%' . $keyword . '%'];
		}
		$stimeFull = $stime !== '' ? (strlen($stime) <= 10 ? $stime . ' 00:00:00' : $stime) : '';
		$etimeFull = $etime !== '' ? (strlen($etime) <= 10 ? $etime . ' 23:59:59' : $etime) : '';
		if ($stimeFull !== '' && $etimeFull !== '') {
			$where['add_time'] = ['between', [$stimeFull, $etimeFull]];
		} elseif ($stimeFull !== '') {
			$where['add_time'] = ['>=', $stimeFull];
		} elseif ($etimeFull !== '') {
			$where['add_time'] = ['<=', $etimeFull];
		}

		$logService = service('log/Category');
		$total = $logService->getCountData($where);
		$list = [];
		if ($total > 0) {
			$list = $logService->getListData($where, '*', $page, $size, ['log_id' => 'desc']);
			$memIds = array_unique(array_filter(array_column($list, 'mem_id')));
			$cateIds = array_filter(array_column($list, 'cate_id'));
			$memberMap = [];
			if (!empty($memIds)) {
				$memberService = service('member/Member');
				$members = $memberService->getListData(
					['mem_id' => count($memIds) === 1 ? reset($memIds) : ['in', array_values($memIds)]],
					'mem_id, avatar, sex, first_name, last_name, nick_name, mobile',
					0,
					0
				);
				foreach ($members as $m) {
					$firstName = $m['first_name'] ?? '';
					$lastName = $m['last_name'] ?? '';
					$m['fullName'] = ($firstName === $lastName) ? $firstName : trim($firstName . ' ' . $lastName);
					$m['avatar'] = $memberService->getAvatar($m['avatar'], $m['sex']);
					$memberMap[$m['mem_id']] = $m;
				}
			}
			$categoryMap = [];
			if (!empty($cateIds)) {
				$categoryMap = service('category/Category')->getListData(['cate_id' => ['in', array_unique($cateIds)]], 'cate_id, name, parent_id');
				$categoryMap = array_column($categoryMap, null, 'cate_id');
			}
			foreach ($list as &$item) {
				$item['member'] = $memberMap[$item['mem_id']] ?? [];
				$item['category'] = $categoryMap[$item['cate_id']] ?? [];
			}
			unset($item);
		}

		$this->assign('total', $total);
		$this->assign('list', $list);
		$this->assign('size', $size);
		$this->assign('typeList', $logService->getTypeList());
		$this->assign('search', [
			'type'    => $type,
			'cate_id' => $cateId,
			'keyword' => $keyword,
			'stime'   => $stime,
			'etime'   => $etime,
		]);
		$this->view();
	}
}