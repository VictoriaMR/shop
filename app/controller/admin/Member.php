<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Member extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '人员列表',
			'log' => '日志',
		];
		$this->_default = '管理人员';
		parent::_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'modify', 'editInfo', 'delete'])) {
				$this->$opn();
			}
		}

		frame('Html')->addJs();
		$search = [
			'status' => (int) iget('status', -1),
			'page' => (int) iget('page', 1),
			'size' => (int) iget('size', 20),
			'name' => trim(iget('name', '')),
			'phone' => trim(iget('phone', '')),
			'stime' => trim(iget('stime', '')),
			'etime' => trim(iget('etime', '')),
		];

		$where = [
			'site_id' => siteId(),
		];
		if ($search['status'] >= 0) {
			$where['status'] = $search['status'];
		}
		if (!empty($search['name'])) {
			$where['first_name,last_name,nick_name'] = ['like', '%'.$search['name'].'%'];
		}
		if (!empty($search['phone'])) {
			$where['mobile'] = ['like', '%'.$search['phone'].'%'];
		}
		if (!empty($search['stime'])) {
			$where['add_time'] = ['>=', $search['stime']];
		}
		if (!empty($search['etime'])) {
			$where['add_time'] = ['<=', $search['etime']];
		}

		$member = service('member/Member');
		$total = $member->getCountData($where);
		if ($total > 0) {
			$fields = 'mem_id,avatar,sex,first_name,last_name,nick_name,mobile,email,status,add_time,login_time';
			$list = $member->getListData($where, $fields, $search['page'], $search['size']);
			foreach ($list as $key => $value) {
				$value['avatar'] = $member->getAvatar($value['avatar'], $value['sex']);
				$list[$key] = $value;
			}
		}

		$this->view([
			'total' => $total,
			'list' => $list ?? [],
			'search' => $search,
		]);
	}

	protected function modify()
	{
		$memId = (int) ipost('mem_id');
		$status = (int) ipost('status');
		if (empty($memId)) {
			$this->error('账户ID不能为空');
		}
		$result = service('member/Member')->updateData($memId, ['status'=>$status, 'update_time'=>now()]);
		if ($result) {
			$this->addLog('修改用户状态-'.$memId.($status==1?'-启用':'-停用'));
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function delete()
	{
		$memId = (int) ipost('mem_id');
		if (empty($memId)) {
			$this->error('账户ID不能为空');
		}
		$result = service('member/Member')->deleteData($memId);
		if ($result) {
			$this->addLog('删除用户-'.$memId);
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}

	protected function getInfo()
	{
		$memId = (int) ipost('mem_id');
		if (empty($memId)) {
			$this->error('账户ID不能为空');
		}
		$info = service('member/Member')->loadData($memId);
		if (empty($info)) {
			$this->error('找不到用户数据');
		}
		$this->addLog('获取用户信息-'.$memId);
		unset($info['password'], $info['salt']);
		$firstName = $info['first_name'] ?? '';
		$lastName = $info['last_name'] ?? '';
		$info['name'] = ($firstName === $lastName) ? $firstName : trim($firstName . ' ' . $lastName);
		$info['nickname'] = $info['nick_name'] ?? '';
		$this->success('获取成功', $info);
	}

	protected function editInfo()
	{
		$mem_id = (int) ipost('mem_id');
		$name = trim(ipost('name'));
		$nickname = trim(ipost('nickname'));
		$mobile = trim(ipost('mobile'));
		$email = trim(ipost('email'));
		$status = (int) ipost('status');
		$password = trim(ipost('password'));
		$repassword = trim(ipost('repassword'));
		if (empty($mem_id) && empty($password)) {
			$this->error('密码不能为空');
		}
		if (!empty($password)) {
			if ($password != $repassword) {
				$this->error('确认密码不匹配');
			}
		}
		$data = [
			'status' => $status,
		];
		if ($name !== '') {
			if (strpos($name, ' ') !== false) {
				list($first, $last) = explode(' ', $name, 2);
				$data['first_name'] = $first;
				$data['last_name'] = $last;
			} else {
				$data['first_name'] = $name;
				$data['last_name'] = '';
			}
		}
		if ($nickname !== '') {
			$data['nick_name'] = $nickname;
		}
		if ($mobile !== '') {
			$data['mobile'] = $mobile;
		}
		if ($email !== '') {
			$data['email'] = $email;
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
			$this->addLog('新增用户-'.$result);
		} else {
			$this->addLog('编辑用户-'.$mem_id);
			$data['update_time'] = now();
			$result = $service->updateData($mem_id, $data);
		}
		if ($result) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	public function log()
	{
		frame('Html')->addJs();

		$type = trim(iget('type', 'all'));
		$name = trim(iget('name', ''));
		$phone = trim(iget('phone', ''));
		$keyword = trim(iget('keyword', ''));
		$ip = trim(iget('ip', ''));
		$stime = trim(iget('stime', ''));
		$etime = trim(iget('etime', ''));
		$page = max(1, (int)iget('page', 1));
		$size = max(1, min(100, (int)iget('size', 20)));

		$where = [
			'site_id' => siteId(),
		];

		// 行为类型筛选
		if ($type === 'login') {
			$where['info'] = ['like', '%登录%'];
		} elseif ($type === 'edit') {
			$where['info'] = ['REGEXP', '编辑|修改|更改'];
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
		if ($stime !== '') {
			$where['add_time'] = ['>=', $stime . (strlen($stime) === 10 ? ' 00:00:00' : '')];
		}
		if ($etime !== '') {
			$where['add_time'] = ['<=', $etime . (strlen($etime) === 10 ? ' 23:59:59' : '')];
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

		$logService = service('log/Login');
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
					'mem_id'   => $mid,
					'avatar'   => siteUrl('/image/common/male.jpg'),
					'nick_name'=> $mid > 0 ? '用户-' . $mid : '系统/未登录',
					'fullName' => '',
					'mobile'   => '-',
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
			'type'    => $type,
			'name'    => $name,
			'keyword' => $keyword,
			'ip'      => $ip,
			'stime'   => $stime,
			'etime'   => $etime,
			'page'    => $page,
			'size'    => $size,
		];

		$this->view([
			'total'   => $total,
			'list'    => $list,
			'typeArr' => $typeArr,
			'search'  => $search,
		]);
	}
}