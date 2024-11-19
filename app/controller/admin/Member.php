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
		if (frame('Request')->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'modify', 'editInfo'])) {
				$this->$opn();
			}
		}

		frame('Html')->addJs();
		$status = (int) iget('status', -1);
		$page = (int) iget('page', 1);
		$size = (int) iget('size', 20);
		$name = trim(iget('name', ''));
		$phone = trim(iget('phone', ''));
		$stime = trim(iget('stime', ''));
		$etime = trim(iget('etime', ''));

		$where = [
			'site_id' => siteId(),
		];
		if ($status >= 0) {
			$where['status'] = $status;
		}
		if (!empty($name)) {
			$where['first_name,last_name,nick_name'] = ['like', '%'.$name.'%'];
		}
		if (!empty($phone)) {
			$where['mobile'] = ['like', '%'.$phone.'%'];
		}

		$member = service('member/Member');
		$total = $member->getCountData($where);
		if ($total > 0) {
			$fields = 'mem_id,avatar,sex,first_name,last_name,nick_name,mobile,email,status,add_time,login_time';
			$list = $member->getListData($where, $fields, $page, $size);
			foreach ($list as $key => $value) {
				$value['avatar'] = $member->getAvatar($value['avatar'], $value['sex']);
				$list[$key] = $value;
			}
		}

		$this->view([
			'total' => $total,
			'list' => $list ?? [],
			'size' => $size,
			'status' => $status,
			'name' => $name,
			'phone' => $phone,
			'stime' => $stime,
			'etime' => $etime,
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
		$data = [
			'remark' => '获取用户信息-'.$memId,
			'type_id' => 3,
		];
		unset($info['password']);
		$this->success($info);
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
		if (!empty($name)) {
			$data['name'] = $name;
		}
		if (!empty($nickname)) {
			$data['nickname'] = $nickname;
		}
		if (!empty($mobile)) {
			$data['mobile'] = $mobile;
		}
		if (!empty($email)) {
			$data['email'] = $email;
		}
		if (!empty($password)) {
			$data['password'] = $password;
		}
		$service = service('member/Member');
		if (empty($mem_id)) {
			$result = $service->create($data);
			$this->addLog('新增用户-'.$result);
		} else {
			$this->addLog('编辑用户-'.$mem_id);
			$result = $service->updateData($mem_id, $data);
		}
		if ($result) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	public function log()
	{
		$name = trim(iget('name', ''));
		$mobile = trim(iget('mobile', ''));
		$stime = trim(iget('stime', ''));
		$etime = trim(iget('etime', ''));
		$size = 50;
		$total = 0;

		$this->view([
			'name' => $name,
			'mobile' => $mobile,
			'stime' => $stime,
			'etime' => $etime,
			'size' => $size,
			'total' => $total,
		]);
	}
}