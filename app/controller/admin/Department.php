<?php

namespace app\controller\admin;

use app\controller\AdminBase;
use app\service\log\Member as LogMember;

class Department extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index'    => '部门管理',
			'position' => '职位管理',
		];
		$this->_default = '管理人员';
		parent::_init();
	}

	/**
	 * 部门管理 (树形多级)
	 */
	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn', '');
			$allowed = ['getDeptInfo', 'editDeptInfo', 'modifyDeptStatus', 'deleteDept', 'sortDept'];
			if (in_array($opn, $allowed, true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();

		$deptService = service('member/Department');
		$list = $deptService->getTreeList();
		$memberCountMap = service('member/MemberDepartment')->getDeptMemberCountMap();

		$this->view([
			'list'           => $list,
			'memberCountMap' => $memberCountMap,
		]);
	}

	protected function getDeptInfo()
	{
		$deptId = (int)ipost('dept_id', 0);
		if ($deptId <= 0) {
			$this->error('部门ID不正确');
		}
		$info = service('member/Department')->loadData($deptId);
		if (empty($info)) {
			$this->error('找不到该部门数据');
		}
		$this->addLog('查看部门信息-' . $deptId, LogMember::TYPE_DEPARTMENT);
		$this->success('获取成功', $info);
	}

	protected function editDeptInfo()
	{
		$deptId   = (int)ipost('dept_id', 0);
		$parentId = max(0, (int)ipost('parent_id', 0));
		$name     = trim(ipost('name', ''));
		$remark   = trim(ipost('remark', ''));
		$status   = (int)ipost('status', 1) === 1 ? 1 : 0;
		$sort     = max(0, (int)ipost('sort', 0));

		if ($name === '') {
			$this->error('部门名称不能为空');
		}

		$deptService = service('member/Department');
		if ($parentId > 0) {
			if ($deptId > 0 && $parentId === $deptId) {
				$this->error('上级部门不能选择当前部门自身');
			}
			$parentInfo = $deptService->loadData($parentId, 'dept_id');
			if (empty($parentInfo)) {
				$this->error('所选上级部门不存在');
			}
			if ($deptId > 0) {
				$descendants = $deptService->getDescendantIds($deptId);
				if (in_array($parentId, $descendants, true)) {
					$this->error('上级部门不能选择当前部门的下级子部门');
				}
			}
		}

		$data = [
			'parent_id' => $parentId,
			'name'      => $name,
			'remark'    => $remark,
			'status'    => $status,
			'sort'      => $sort,
		];

		if ($deptId <= 0) {
			$newId = $deptService->insertData($data, true);
			if ($newId) {
				$this->addLog('新增部门-' . $newId . '(' . $name . ')', LogMember::TYPE_DEPARTMENT);
				$this->success('新增成功');
			}
		} else {
			$data['update_time'] = now();
			$rst = $deptService->updateData($deptId, $data);
			if ($rst !== false) {
				$this->addLog('编辑部门-' . $deptId . '(' . $name . ')', LogMember::TYPE_DEPARTMENT);
				$this->success('保存成功');
			}
		}
		$this->error('操作失败');
	}

	protected function modifyDeptStatus()
	{
		$deptId = (int)ipost('dept_id', 0);
		$status = (int)ipost('status', 0) === 1 ? 1 : 0;
		if ($deptId <= 0) {
			$this->error('部门ID不能为空');
		}
		$rst = service('member/Department')->updateData($deptId, [
			'status'      => $status,
			'update_time' => now(),
		]);
		if ($rst !== false) {
			$this->addLog('修改部门状态-' . $deptId . ($status === 1 ? '-启用' : '-停用'), LogMember::TYPE_DEPARTMENT);
			$this->success('状态更新成功');
		}
		$this->error('状态更新失败');
	}

	protected function sortDept()
	{
		$deptId   = (int)ipost('dept_id', 0);
		$parentId = max(0, (int)ipost('parent_id', 0));
		$type     = trim(ipost('type', ''));
		if ($deptId <= 0 || $type === '') {
			$this->error('排序参数不正确');
		}
		$rst = service('tool/Sort')->sort('department', $deptId, $type, 'dept_id', 'sort', ['parent_id' => $parentId]);
		if ($rst) {
			$this->addLog('部门排序-' . $deptId . '-' . $type, LogMember::TYPE_DEPARTMENT);
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}

	protected function deleteDept()
	{
		$deptId = (int)ipost('dept_id', 0);
		if ($deptId <= 0) {
			$this->error('部门ID不能为空');
		}
		$deptService = service('member/Department');
		$info = $deptService->loadData($deptId, 'dept_id,name');
		if (empty($info)) {
			$this->error('部门不存在');
		}
		if ($deptService->getCountData(['parent_id' => $deptId]) > 0) {
			$this->error('该部门下存在子部门，请先删除或移走子部门');
		}
		if (service('member/MemberDepartment')->getCountData(['dept_id' => $deptId]) > 0) {
			$this->error('该部门下已关联人员，请先解除关联后再删除');
		}
		$rst = $deptService->deleteData($deptId);
		if ($rst) {
			service('controller/Permission')->deleteData(['target_type' => 1, 'target_id' => $deptId]);
			$this->addLog('删除部门-' . $deptId . '(' . ($info['name'] ?? '') . ')', LogMember::TYPE_DEPARTMENT);
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	/**
	 * 职位管理 (单层列表)
	 */
	public function position()
	{
		if (isPost()) {
			$opn = ipost('opn', '');
			$allowed = ['getPosInfo', 'editPosInfo', 'modifyPosStatus', 'deletePos', 'sortPos'];
			if (in_array($opn, $allowed, true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();

		$search = [
			'status'  => (int)iget('status', -1),
			'keyword' => trim(iget('keyword', '')),
			'page'    => max(1, (int)iget('page', 1)),
			'size'    => max(1, min(100, (int)iget('size', 20))),
		];

		$where = [];
		if ($search['status'] >= 0) {
			$where['status'] = $search['status'];
		}
		if ($search['keyword'] !== '') {
			$where['name,remark'] = ['like', '%' . $search['keyword'] . '%'];
		}

		$posService = service('member/Position');
		$total = $posService->getCountData($where);
		$list = [];
		if ($total > 0) {
			$list = $posService->getList($where, $search['page'], $search['size']);
		}
		$memberCountMap = service('member/MemberDepartment')->getPosMemberCountMap();

		$this->view([
			'total'          => $total,
			'list'           => $list,
			'search'         => $search,
			'memberCountMap' => $memberCountMap,
		]);
	}

	protected function getPosInfo()
	{
		$posId = (int)ipost('pos_id', 0);
		if ($posId <= 0) {
			$this->error('职位ID不正确');
		}
		$info = service('member/Position')->loadData($posId);
		if (empty($info)) {
			$this->error('找不到该职位数据');
		}
		$this->addLog('查看职位信息-' . $posId, LogMember::TYPE_POSITION);
		$this->success('获取成功', $info);
	}

	protected function editPosInfo()
	{
		$posId  = (int)ipost('pos_id', 0);
		$name   = trim(ipost('name', ''));
		$remark = trim(ipost('remark', ''));
		$status = (int)ipost('status', 1) === 1 ? 1 : 0;
		$sort   = max(0, (int)ipost('sort', 0));

		if ($name === '') {
			$this->error('职位名称不能为空');
		}

		$posService = service('member/Position');
		$data = [
			'name'   => $name,
			'remark' => $remark,
			'status' => $status,
			'sort'   => $sort,
		];

		if ($posId <= 0) {
			$newId = $posService->insertData($data, true);
			if ($newId) {
				$this->addLog('新增职位-' . $newId . '(' . $name . ')', LogMember::TYPE_POSITION);
				$this->success('新增成功');
			}
		} else {
			$data['update_time'] = now();
			$rst = $posService->updateData($posId, $data);
			if ($rst !== false) {
				$this->addLog('编辑职位-' . $posId . '(' . $name . ')', LogMember::TYPE_POSITION);
				$this->success('保存成功');
			}
		}
		$this->error('操作失败');
	}

	protected function modifyPosStatus()
	{
		$posId  = (int)ipost('pos_id', 0);
		$status = (int)ipost('status', 0) === 1 ? 1 : 0;
		if ($posId <= 0) {
			$this->error('职位ID不能为空');
		}
		$rst = service('member/Position')->updateData($posId, [
			'status'      => $status,
			'update_time' => now(),
		]);
		if ($rst !== false) {
			$this->addLog('修改职位状态-' . $posId . ($status === 1 ? '-启用' : '-停用'), LogMember::TYPE_POSITION);
			$this->success('状态更新成功');
		}
		$this->error('状态更新失败');
	}

	protected function sortPos()
	{
		$posId = (int)ipost('pos_id', 0);
		$type  = trim(ipost('type', ''));
		if ($posId <= 0 || $type === '') {
			$this->error('排序参数不正确');
		}
		$rst = service('tool/Sort')->sort('position', $posId, $type, 'pos_id', 'sort');
		if ($rst) {
			$this->addLog('职位排序-' . $posId . '-' . $type, LogMember::TYPE_POSITION);
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}

	protected function deletePos()
	{
		$posId = (int)ipost('pos_id', 0);
		if ($posId <= 0) {
			$this->error('职位ID不能为空');
		}
		$posService = service('member/Position');
		$info = $posService->loadData($posId, 'pos_id,name');
		if (empty($info)) {
			$this->error('职位不存在');
		}
		if (service('member/MemberDepartment')->getCountData(['pos_id' => $posId]) > 0) {
			$this->error('该职位下已关联人员，请先解除关联后再删除');
		}
		$rst = $posService->deleteData($posId);
		if ($rst) {
			$this->addLog('删除职位-' . $posId . '(' . ($info['name'] ?? '') . ')', LogMember::TYPE_POSITION);
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}
}
