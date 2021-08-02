<?php

namespace app\controller\admin;
use app\controller\Controller;

class MemberController extends Controller
{
	public function __construct()
	{
        $this->_arr = [
            'index' => '人员列表',
            'log' => '日志',
        ];
        $this->_default = '管理人员';
		$this->_init();
	}

	public function index()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'modify', 'editInfo'])) {
				$this->$opn();
			}
		}

		html()->addJs();
		$status = (int) iget('status', -1);
		$page = (int) iget('page', 1);
		$size = (int) iget('size', 20);
		$name = trim(iget('name'));
		$phone = trim(iget('phone'));
		$stime = trim(iget('stime'));
		$etime = trim(iget('etime'));

		$where = [];
		if ($status >= 0) {
			$where['status'] = $status;
		}
		if (!empty($name)) {
			$where['name,nickname'] = ['like', '%'.$name.'%'];
		}
		if (!empty($phone)) {
			$where['mobile'] = ['like', '%'.$phone.'%'];
		}

		$memberService = make('app/service/admin/MemberService');
		$total = $memberService->getCountData($where);
		if ($total > 0) {
			$fields = 'mem_id,name,nickname,phone,email,status,salt,create_at';
			$list = $memberService->getListData($where, '*', $page, $size);
			foreach ($list as $key => $value) {
				$value['avatar'] = $memberService->getAvatar($value['avatar'], $value['sex']);
				$list[$key] = $value;
			}
		}

		$this->assign('total', $total);
		$this->assign('list', $list ?? []);
		$this->assign('size', $size);
		$this->assign('status', $status);
		$this->assign('name', $name);
		$this->assign('phone', $phone);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);

		$this->view();
	}

	protected function modify()
	{
		$memId = (int) ipost('mem_id');
		$status = (int) ipost('status');
		if (empty($memId)) {
			$this->error('账户ID不能为空');
		}
		$result = make('app/service/admin/MemberService')->updateData($memId, ['status'=>$status, 'update_time'=>now()]);
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
		$info = make('app/service/admin/MemberService')->loadData($memId);
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
		$service = make('app/service/admin/MemberService');
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
		$page = (int) iget('page', 1);
		$size = (int) iget('size', 50);
		$typeId = iget('type_id', -1);
		$name = trim(iget('name'));
		$mobile = trim(iget('mobile'));
		$stime = trim(iget('stime'));
		$etime = trim(iget('etime'));

		$loggerService = make('app/service/admin/LoggerService');
		$where = [];
		if ($typeId >= 0) {
			$where['type'] = (int) $typeId;
		}
		$memberService = make('app/service/admin/MemberService');
		if (!empty($name)) {
			$memIdArr = $memberService->getMemIdsByName($name);
			if (!empty($memIdArr)) {
				$where['mem_id'] = ['in', $memIdArr];
			} else {
				$where = ['mem_id' => 0];
			}
		}
		if (!empty($mobile)) {
			$tempArr = $memIdArr ?? [];
			$memIdArr = $memberService->getMemIdsByMobile($mobile);
			if (!empty($memIdArr)) {
				$where['mem_id'] = ['in', array_unique(array_merge($tempArr, $memIdArr))];
			} else {
				$where = ['mem_id' => 0];
			}
		}

		$total = $loggerService->getCountData($where);
		if ($total > 0) {
			$list = $loggerService->getList($where, '*', $page, $size);
		}

		$this->assign('typeArr', $loggerService->getTypeList());
		$this->assign('typeId', $typeId);
		$this->assign('total', $total);
		$this->assign('list', $list ?? []);
		$this->assign('size', $size);
		$this->assign('name', $name);
		$this->assign('mobile', $mobile);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);

		$this->view();
	}
}