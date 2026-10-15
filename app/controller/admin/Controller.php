<?php

namespace app\controller\admin;
use app\controller\AdminBase;
use app\model\controller\Permission as PermissionModel;
use app\model\log\Member as LogMemberModel;

class Controller extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index'      => '功能管理',
			'permission' => '权限管理',
		];
		$this->_default = '系统设置';
		parent::_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'editInfo', 'deleteInfo', 'sortInfo'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();

		$list = service('controller/Controller')->getList();
		$this->assign('list', $list);
		$this->view();
	}

	/**
	 * 权限管理：支持按部门或个人分配整个控制器或具体 public 方法权限
	 */
	public function permission()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getTargetPerm', 'saveTargetPerm'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();

		$permService = service('controller/Permission');
		$deptService = service('member/Department');
		$posService = service('member/Position');
		$memDeptService = service('member/MemberDepartment');

		$deptList = $deptService->getTreeList();
		$deptMap = $deptService->getNameMap();
		$posMap = $posService->getNameMap();

		$memberList = service('member/Member')->getListData(
			[],
			'mem_id,first_name,last_name,nick_name,mobile,status',
			0,
			0,
			['mem_id' => 'asc']
		);
		if (!empty($memberList)) {
			$memIds = array_column($memberList, 'mem_id');
			$relMap = $memDeptService->getMapByMemIds($memIds);
			foreach ($memberList as &$m) {
				$mid = (int)$m['mem_id'];
				$rel = $relMap[$mid] ?? null;
				$dId = $rel ? (int)$rel['dept_id'] : 0;
				$pId = $rel ? (int)$rel['pos_id'] : 0;
				$m['dept_id']   = $dId;
				$m['pos_id']    = $pId;
				$m['dept_name'] = $deptMap[$dId] ?? '';
				$m['pos_name']  = $posMap[$pId] ?? '';
				$m['is_super']  = $permService->isSuperAdmin($mid);
			}
			unset($m);
		}

		$this->assign('deptList', $deptList);
		$this->assign('memberList', $memberList);
		$this->assign('deptPermCounts', $permService->getCountMapByType(PermissionModel::TYPE_DEPARTMENT));
		$this->assign('memPermCounts', $permService->getCountMapByType(PermissionModel::TYPE_MEMBER));
		$this->assign('catalog', $permService->getControllerMethodCatalog());
		$this->view();
	}

	protected function getTargetPerm()
	{
		$targetType = (int)ipost('target_type', PermissionModel::TYPE_DEPARTMENT);
		$targetId = (int)ipost('target_id', 0);
		if (!in_array($targetType, [PermissionModel::TYPE_DEPARTMENT, PermissionModel::TYPE_MEMBER], true) || $targetId <= 0) {
			$this->error('参数不正确');
		}

		$permService = service('controller/Permission');
		$isSuper = ($targetType === PermissionModel::TYPE_MEMBER && $permService->isSuperAdmin($targetId));
		$ownPerms = $isSuper ? [] : $permService->getTargetPermList($targetType, $targetId);
		$deptPerms = [];
		$deptId = 0;

		if ($targetType === PermissionModel::TYPE_MEMBER && !$isSuper) {
			$rel = service('member/MemberDepartment')->getByMemId($targetId);
			$deptId = (int)($rel['dept_id'] ?? 0);
			if ($deptId > 0) {
				$deptPerms = $permService->getTargetPermList(PermissionModel::TYPE_DEPARTMENT, $deptId);
			}
		}

		$this->success('获取成功', [
			'target_type' => $targetType,
			'target_id'   => $targetId,
			'is_super'    => $isSuper,
			'dept_id'     => $deptId,
			'own_perms'   => $ownPerms,
			'dept_perms'  => $deptPerms,
		]);
	}

	protected function saveTargetPerm()
	{
		$targetType = (int)ipost('target_type', PermissionModel::TYPE_DEPARTMENT);
		$targetId = (int)ipost('target_id', 0);
		$rulesRaw = ipost('rules', '');

		if (!in_array($targetType, [PermissionModel::TYPE_DEPARTMENT, PermissionModel::TYPE_MEMBER], true) || $targetId <= 0) {
			$this->error('参数不正确');
		}

		$permService = service('controller/Permission');
		if ($targetType === PermissionModel::TYPE_MEMBER && $permService->isSuperAdmin($targetId)) {
			$this->error('超级管理员默认拥有全部权限，无需修改');
		}

		$rules = is_string($rulesRaw) ? (json_decode($rulesRaw, true) ?: []) : (is_array($rulesRaw) ? $rulesRaw : []);
		$rst = $permService->saveTargetPermissions($targetType, $targetId, $rules);
		if (!$rst) {
			$this->error('保存权限失败');
		}

		$savedList = $permService->getTargetPermList($targetType, $targetId);
		$savedCount = count($savedList);

		if ($targetType === PermissionModel::TYPE_DEPARTMENT) {
			$deptInfo = service('member/Department')->loadData($targetId, 'name');
			$targetName = $deptInfo['name'] ?? ('ID:' . $targetId);
			$this->addLog('分配部门权限：' . $targetName . ' (ID:' . $targetId . ', 共' . $savedCount . '项)', LogMemberModel::TYPE_DEPARTMENT);
		} else {
			$memInfo = service('member/Member')->loadData($targetId, 'nick_name,first_name,last_name');
			$targetName = ($memInfo['nick_name'] ?? '') ?: trim(($memInfo['first_name'] ?? '') . ($memInfo['last_name'] ?? '')) ?: ('ID:' . $targetId);
			$this->addLog('分配个人权限：' . $targetName . ' (ID:' . $targetId . ', 共' . $savedCount . '项)', LogMemberModel::TYPE_MEMBER);
		}

		$this->success('权限配置已保存', [
			'count'     => $savedCount,
			'own_perms' => $savedList,
		]);
	}

	protected function getInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$info = service('controller/Controller')->loadData($id);
		if (empty($info)) {
			$this->error('查询不到有效数据');
		}
		$this->success('获取成功', $info);
	}

	protected function editInfo()
	{
		$id = (int)ipost('con_id', 0);
		$name = trim(ipost('name', ''));
		$value = trim(ipost('value', ''));
		$icon = trim(ipost('icon', ''));
		$sort = (int)ipost('sort', 0);
		if ($name === '' || $value === '' || $icon === '') {
			$this->error('参数不正确');
		}
		$data = [
			'name'  => $name,
			'value' => $value,
			'icon'  => $icon,
			'sort'  => $sort,
		];
		if ($id <= 0) {
			$data['parent_id'] = (int)ipost('parent_id', 0);
			$rst = service('controller/Controller')->insert($data);
		} else {
			$rst = service('controller/Controller')->updateData($id, $data);
		}
		if ($rst !== false) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function deleteInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$service = service('controller/Controller');
		if ($service->getCountData(['parent_id' => $id])) {
			$this->error('此功能下有子功能, 不能删除');
		}
		$res = $service->deleteData($id);
		if ($res) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function sortInfo()
	{
		$id = (int)ipost('id');
		$type = trim(ipost('type', ''));
		$parentId = (int)ipost('parent_id', 0);
		if (empty($id)) {
			$this->error('参数不正确');
		}
		if ($type !== '') {
			$rst = service('tool/Sort')->sort('controller', $id, $type, 'con_id', 'sort', ['parent_id' => $parentId]);
		} else {
			$sort = (int)ipost('sort', 0);
			$rst = service('controller/Controller')->updateData($id, ['sort' => $sort]);
		}
		if ($rst) {
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}
}