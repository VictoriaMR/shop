<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Member extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '人员列表',
			'log'   => '日志',
		];
		$this->_default = '管理人员';
		parent::_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'modify', 'editInfo', 'delete'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();

		$search = [
			'status'  => (int)iget('status', -1),
			'dept_id' => max(0, (int)iget('dept_id', 0)),
			'pos_id'  => max(0, (int)iget('pos_id', 0)),
			'page'    => max(1, (int)iget('page', 1)),
			'size'    => max(1, min(100, (int)iget('size', 20))),
			'name'    => trim(iget('name', '')),
			'phone'   => trim(iget('phone', '')),
			'stime'   => trim(iget('stime', '')),
			'etime'   => trim(iget('etime', '')),
		];

		$where = [
			'site_id' => siteId(),
		];
		if ($search['status'] >= 0) {
			$where['status'] = $search['status'];
		}
		if ($search['name'] !== '') {
			$where['first_name,last_name,nick_name'] = ['like', '%' . $search['name'] . '%'];
		}
		if ($search['phone'] !== '') {
			$where['mobile'] = ['like', '%' . $search['phone'] . '%'];
		}
		$stimeFull = $search['stime'] !== '' ? ($search['stime'] . (strlen($search['stime']) === 10 ? ' 00:00:00' : '')) : '';
		$etimeFull = $search['etime'] !== '' ? ($search['etime'] . (strlen($search['etime']) === 10 ? ' 23:59:59' : '')) : '';
		if ($stimeFull !== '' && $etimeFull !== '') {
			$where['add_time'] = ['between', [$stimeFull, $etimeFull]];
		} elseif ($stimeFull !== '') {
			$where['add_time'] = ['>=', $stimeFull];
		} elseif ($etimeFull !== '') {
			$where['add_time'] = ['<=', $etimeFull];
		}

		$deptService = service('member/Department');
		$posService = service('member/Position');
		$relService = service('member/MemberDepartment');

		$deptList = $deptService->getTreeList();
		$deptMap = array_column($deptList, null, 'dept_id');
		$posList = $posService->getList();
		$posMap = array_column($posList, null, 'pos_id');

		// 按部门或职位关联表过滤人员
		if ($search['dept_id'] > 0 || $search['pos_id'] > 0) {
			$relWhere = [];
			if ($search['dept_id'] > 0) {
				$deptIds = array_merge([$search['dept_id']], $deptService->getDescendantIds($search['dept_id']));
				$relWhere['dept_id'] = count($deptIds) === 1 ? $deptIds[0] : ['in', $deptIds];
			}
			if ($search['pos_id'] > 0) {
				$relWhere['pos_id'] = $search['pos_id'];
			}
			$relRows = $relService->getListData($relWhere, 'mem_id', 0, 0);
			if (empty($relRows)) {
				$where['mem_id'] = -1;
			} else {
				$filterMemIds = array_values(array_unique(array_column($relRows, 'mem_id')));
				$where['mem_id'] = count($filterMemIds) === 1 ? $filterMemIds[0] : ['in', $filterMemIds];
			}
		}

		$member = service('member/Member');
		$total = $member->getCountData($where);
		$list = [];
		if ($total > 0) {
			$fields = 'mem_id,avatar,sex,first_name,last_name,nick_name,mobile,email,status,add_time,login_time';
			$list = $member->getListData($where, $fields, $search['page'], $search['size'], ['mem_id' => 'desc']);
			$memIds = array_column($list, 'mem_id');
			$relMap = $relService->getMapByMemIds($memIds);

			foreach ($list as $key => $value) {
				$mid = (int)$value['mem_id'];
				$rel = $relMap[$mid] ?? [];
				$did = (int)($rel['dept_id'] ?? 0);
				$pid = (int)($rel['pos_id'] ?? 0);

				$value['avatar']    = $member->getAvatar($value['avatar'], $value['sex']);
				$value['dept_id']   = $did;
				$value['pos_id']    = $pid;
				$value['dept_name'] = $deptMap[$did]['name'] ?? '';
				$value['dept_path'] = $deptMap[$did]['path_name'] ?? '';
				$value['pos_name']  = $posMap[$pid]['name'] ?? '';
				$list[$key] = $value;
			}
		}

		$this->view([
			'total'    => $total,
			'list'     => $list,
			'search'   => $search,
			'deptList' => $deptList,
			'posList'  => $posList,
		]);
	}

	protected function modify()
	{
		$memId = (int)ipost('mem_id');
		$status = (int)ipost('status') === 1 ? 1 : 0;
		if (empty($memId)) {
			$this->error('账户ID不能为空');
		}
		$result = service('member/Member')->updateData($memId, ['status' => $status, 'update_time' => now()]);
		if ($result) {
			$this->addLog('修改用户状态-' . $memId . ($status === 1 ? '-启用' : '-停用'));
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function delete()
	{
		$memId = (int)ipost('mem_id');
		if (empty($memId)) {
			$this->error('账户ID不能为空');
		}
		if (service('controller/Permission')->isSuperAdmin($memId)) {
			$this->error('系统超级管理员不可删除');
		}
		$result = service('member/Member')->deleteData($memId);
		if ($result) {
			service('member/MemberDepartment')->deleteData(['mem_id' => $memId]);
			service('controller/Permission')->deleteData(['target_type' => 2, 'target_id' => $memId]);
			$this->addLog('删除用户-' . $memId);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}

	protected function getInfo()
	{
		$memId = (int)ipost('mem_id');
		if (empty($memId)) {
			$this->error('账户ID不能为空');
		}
		$info = service('member/Member')->loadData($memId);
		if (empty($info)) {
			$this->error('找不到用户数据');
		}
		$this->addLog('获取用户信息-' . $memId);
		unset($info['password'], $info['salt']);
		$firstName = $info['first_name'] ?? '';
		$lastName = $info['last_name'] ?? '';
		$info['name'] = ($firstName === $lastName) ? $firstName : trim($firstName . ' ' . $lastName);
		$info['nickname'] = $info['nick_name'] ?? '';

		$rel = service('member/MemberDepartment')->loadData(['mem_id' => $memId], 'dept_id,pos_id');
		$info['dept_id'] = (int)($rel['dept_id'] ?? 0);
		$info['pos_id']  = (int)($rel['pos_id'] ?? 0);

		$this->success('获取成功', $info);
	}

	protected function editInfo()
	{
		$mem_id     = (int)ipost('mem_id');
		$name       = trim(ipost('name', ''));
		$nickname   = trim(ipost('nickname', ''));
		$mobile     = trim(ipost('mobile', ''));
		$email      = trim(ipost('email', ''));
		$deptId     = max(0, (int)ipost('dept_id', 0));
		$posId      = max(0, (int)ipost('pos_id', 0));
		$status     = (int)ipost('status') === 1 ? 1 : 0;
		$password   = trim(ipost('password', ''));
		$repassword = trim(ipost('repassword', ''));

		if ($name === '') {
			$this->error('名称不能为空');
		}
		if ($nickname === '') {
			$this->error('昵称不能为空');
		}
		if ($mobile === '') {
			$this->error('手机号码不能为空');
		}
		if ($email === '') {
			$this->error('邮箱不能为空');
		}
		if (empty($mem_id) && $password === '') {
			$this->error('密码不能为空');
		}
		if ($password !== '' && $password !== $repassword) {
			$this->error('确认密码不匹配');
		}

		$data = [
			'status'    => $status,
			'nick_name' => $nickname,
			'mobile'    => $mobile,
			'email'     => $email,
		];
		if (strpos($name, ' ') !== false) {
			list($first, $last) = explode(' ', $name, 2);
			$data['first_name'] = $first;
			$data['last_name'] = $last;
		} else {
			$data['first_name'] = $name;
			$data['last_name'] = '';
		}

		$service = service('member/Member');
		if ($password !== '') {
			if (!empty($mem_id)) {
				$info = $service->loadData($mem_id, 'salt');
				$salt = $info['salt'] ?? '';
				if (empty($salt)) {
					$salt = randString(4);
					$data['salt'] = $salt;
				}
				$data['password'] = $service->getPassword($password, $salt);
			} else {
				$data['password'] = $password;
			}
		}
		if (empty($mem_id)) {
			$result = $service->create($data);
			$targetMemId = (int)$result;
			if ($targetMemId > 0) {
				service('member/MemberDepartment')->bindMember($targetMemId, $deptId, $posId);
				$this->addLog('新增用户-' . $targetMemId);
			}
		} else {
			$data['update_time'] = now();
			$result = $service->updateData($mem_id, $data);
			if ($result !== false) {
				service('member/MemberDepartment')->bindMember($mem_id, $deptId, $posId);
				$this->addLog('编辑用户-' . $mem_id);
			}
		}
		if ($result !== false && $result) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	public function log()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		$logType = (int)iget('log_type', -1);
		$type    = trim(iget('type', 'all'));
		$name    = trim(iget('name', ''));
		$phone   = trim(iget('phone', ''));
		$keyword = trim(iget('keyword', ''));
		$ip      = trim(iget('ip', ''));
		$stime   = trim(iget('stime', ''));
		$etime   = trim(iget('etime', ''));
		$page    = max(1, (int)iget('page', 1));
		$size    = max(1, min(100, (int)iget('size', 20)));

		$where = [
			'site_id' => siteId(),
		];

		// 模块类型筛选 (0:人员管理, 1:部门管理, 2:职位管理)
		if ($logType >= 0) {
			$where['type'] = $logType;
		}

		// 行为类型筛选
		if ($type === 'login') {
			$where['info'] = ['like', '%登录%'];
		} elseif ($type === 'edit') {
			$where['info'] = ['REGEXP', '编辑|修改|更改|排序'];
		} elseif ($type === 'add') {
			$where['info'] = ['like', '%新增%'];
		} elseif ($type === 'del') {
			$where['info'] = ['like', '%删除%'];
		} elseif ($type === 'query') {
			$where['info'] = ['REGEXP', '获取|查看'];
		}

		// 操作内容关键字
		if ($keyword !== '') {
			$where['info'] = ['like', '%' . $keyword . '%'];
		}

		// IP 地址
		if ($ip !== '') {
			$where['ip'] = $ip;
		}

		// 时间区间
		$stimeFull = $stime !== '' ? ($stime . (strlen($stime) === 10 ? ' 00:00:00' : '')) : '';
		$etimeFull = $etime !== '' ? ($etime . (strlen($etime) === 10 ? ' 23:59:59' : '')) : '';
		if ($stimeFull !== '' && $etimeFull !== '') {
			$where['add_time'] = ['between', [$stimeFull, $etimeFull]];
		} elseif ($stimeFull !== '') {
			$where['add_time'] = ['>=', $stimeFull];
		} elseif ($etimeFull !== '') {
			$where['add_time'] = ['<=', $etimeFull];
		}

		// 按人员姓名/昵称/手机号筛选
		$memberService = service('member/Member');
		if ($name !== '' || $phone !== '') {
			$mWhere = ['site_id' => siteId()];
			if ($name !== '') {
				$mWhere['first_name,last_name,nick_name'] = ['like', '%' . $name . '%'];
			}
			if ($phone !== '') {
				$mWhere['mobile'] = ['like', '%' . $phone . '%'];
			}
			$matchMembers = $memberService->getListData($mWhere, 'mem_id', 0, 0);
			if (empty($matchMembers)) {
				$where['mem_id'] = -1;
			} else {
				$matchIds = array_column($matchMembers, 'mem_id');
				$where['mem_id'] = count($matchIds) === 1 ? $matchIds[0] : ['in', $matchIds];
			}
		}

		$logService = service('log/Member');
		$moduleTypes = $logService->getTypeList();
		$total = $logService->getCountData($where);
		$list = [];
		if ($total > 0) {
			$list = $logService->getListData($where, '*', $page, $size, ['log_id' => 'desc']);
			// 批量关联人员基本信息，消除 N+1 查询
			$memIds = array_unique(array_filter(array_column($list, 'mem_id')));
			$memberMap = [];
			if (!empty($memIds)) {
				$mWhere = [
					'site_id' => siteId(),
					'mem_id'  => count($memIds) === 1 ? reset($memIds) : ['in', array_values($memIds)],
				];
				$members = $memberService->getListData($mWhere, 'mem_id,avatar,sex,first_name,last_name,nick_name,mobile', 0, 0);
				foreach ($members as $m) {
					$firstName = $m['first_name'] ?? '';
					$lastName = $m['last_name'] ?? '';
					$m['fullName'] = ($firstName === $lastName) ? $firstName : trim($firstName . ' ' . $lastName);
					$m['avatar'] = $memberService->getAvatar($m['avatar'], $m['sex']);
					$memberMap[$m['mem_id']] = $m;
				}
			}
			foreach ($list as &$item) {
				$mid = (int)$item['mem_id'];
				$item['member'] = $memberMap[$mid] ?? [
					'mem_id'    => $mid,
					'avatar'    => isMobile() ? '' : siteUrl('image/common/male.jpg'),
					'nick_name' => $mid > 0 ? '用户-' . $mid : '系统/未登录',
					'fullName'  => '',
					'mobile'    => '-',
				];
			}
			unset($item);
		}

		$typeArr = [
			'all'   => '全部行为',
			'login' => '登录记录',
			'edit'  => '修改变更',
			'add'   => '新增管理',
			'del'   => '删除操作',
			'query' => '数据查看',
		];

		$search = [
			'log_type' => $logType,
			'type'     => $type,
			'name'     => $name,
			'phone'    => $phone,
			'keyword'  => $keyword,
			'ip'       => $ip,
			'stime'    => $stime,
			'etime'    => $etime,
			'page'     => $page,
			'size'     => $size,
		];

		$this->view([
			'total'       => $total,
			'list'        => $list,
			'typeArr'     => $typeArr,
			'moduleTypes' => $moduleTypes,
			'search'      => $search,
		]);
	}
}