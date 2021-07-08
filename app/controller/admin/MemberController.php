<?php

namespace app\controller\admin;

use app\controller\Controller;
use frame\Html;

class MemberController extends Controller
{
	public function __construct()
	{
        $this->_arr = [
            'index' => '人员列表',
            'loginLog' => '日志',
        ];
        $this->_default = '管理人员';
		$this->_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'modify', 'editInfo'])) {
				$this->$opn();
			}
		}

		Html::addJs();
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

		$memberService = make('App/Services/Admin/MemberService');
		$total = $memberService->getTotal($where);
		if ($total > 0) {
			$list = $memberService->getList($where, $page, $size);
		}

		$this->assign('total', $total);
		$this->assign('list', $list ?? []);
		$this->assign('size', $size);
		$this->assign('status', $status);
		$this->assign('name', $name);
		$this->assign('phone', $phone);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);

		return view();
	}

	protected function modify()
	{
		$memId = (int) ipost('mem_id');
		$status = (int) ipost('status');
		if (empty($memId)) {
			$this->error('账户ID不能为空');
		}
		$result = make('App/Services/Admin/MemberService')->updateDataById($memId, ['status' => $status]);
		if ($result) {
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
		$info = make('App/Services/Admin/MemberService')->getInfo($memId);
		if (empty($info)) {
			$this->error('找不到用户数据');
		}
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
		$memberService = make('App/Services/Admin/MemberService');
		if (empty($mem_id)) {
			$result = $memberService->create($data);
		} else {
			$result = $memberService->updateDataById($mem_id, $data);
		}
		if ($result) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	public function loginLog()
	{
		$page = (int) iget('page', 1);
		$size = (int) iget('size', 50);
		$typeId = iget('type_id');
		$name = trim(iget('name'));
		$mobile = trim(iget('mobile'));
		$stime = trim(iget('stime'));
		$etime = trim(iget('etime'));

		$loggerService = make('App\Services\Admin\LogService');
		$where = [];
		if ($typeId >= 0) {
			$where['type_id'] = (int) $typeId;
		}
		$memberService = make('App/Services/Admin/MemberService');
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

		$total = $loggerService->getTotal($where);
		if ($total > 0) {
			$list = $loggerService->getList($where, $page, $size);
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

		return view();
	}
}