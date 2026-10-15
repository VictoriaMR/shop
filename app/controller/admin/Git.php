<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Git extends AdminBase
{
	private $git_library = ['shop'];

	public function __construct()
	{
		$this->_arr = [
			'index'  => 'Git版本库管理',
			'gitLog' => 'Git版本日志',
		];
		$this->_default = '版本管理';
		parent::_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['updateGit', 'releaseGit'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();

		$list = [];
		$git = service('admin/Git');
		$branch = $git->getCurrentBranch();
		foreach ($this->git_library as $value) {
			$info = $git->loadData(['library' => $value], 'git_id,release_time,commit_time,author,info,commit,status', ['git_id' => 'desc']);
			if (empty($info)) {
				$info = [
					'git_id'       => 0,
					'status'       => -1,
					'commit'       => '',
					'author'       => '',
					'info'         => '',
					'commit_time'  => '',
					'release_time' => '',
				];
			}
			$info['name']          = $value;
			$info['branch']        = $branch ?: 'master';
			$info['total_count']   = $git->getCountData(['library' => $value]);
			$info['pending_count'] = $git->getCountData(['library' => $value, 'status' => 0]);
			$info['status_text']   = $this->getStatus($info['status'] ?? -1);
			$list[] = $info;
		}
		$this->assign('list', $list);
		$this->view();
	}

	private function getStatus($status)
	{
		$arr = [
			0 => '待发布',
			1 => '已发布',
		];
		return $arr[$status] ?? '--';
	}

	public function gitLog()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['updateGit', 'releaseGit'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();
		$id      = trim((string)iget('id', ''));
		$status  = (int)iget('status', -1);
		$keyword = trim((string)iget('keyword', ''));
		$page    = max(1, (int)iget('page', 1));
		$size    = max(1, (int)iget('size', 20));

		$where = [];
		if ($id !== '') {
			$where['library'] = $id;
		}
		if ($status === 0 || $status === 1) {
			$where['status'] = $status;
		}
		if ($keyword !== '') {
			$where['commit,author,info'] = ['like', '%' . $keyword . '%'];
		}

		$git = service('admin/Git');
		$total = $git->getCountData($where);
		$list = [];
		if ($total > 0) {
			$list = $git->getListData($where, '*', $page, $size, ['git_id' => 'desc']);
		}

		$this->assign('id', $id);
		$this->assign('status', $status);
		$this->assign('keyword', $keyword);
		$this->assign('size', $size);
		$this->assign('total', $total);
		$this->assign('list', $list);
		$this->assign('git_library', $this->git_library);
		$this->view();
	}

	private function updateGit()
	{
		$id = trim((string)ipost('id', ''));
		if ($id === '' && !empty($this->git_library)) {
			$id = $this->git_library[0];
		}
		if (!in_array($id, $this->git_library, true)) {
			$this->error('版本库不合法');
		}
		$msg = '';
		$rst = service('admin/Git')->upateLibrary($id, $msg);
		if ($rst) {
			$this->success($msg ?: '更新成功');
		}
		$this->error($msg ?: '更新失败');
	}

	private function releaseGit()
	{
		$id = (int)ipost('id', 0);
		if ($id <= 0) {
			$this->error('参数不正确');
		}
		$git = service('admin/Git');
		$info = $git->loadData($id);
		if (empty($info)) {
			$this->error('找不到版本记录');
		}
		$rst = $git->updateData(
			['library' => $info['library'], 'git_id' => ['<=', (int)$info['git_id']], 'status' => 0],
			['status' => 1, 'release_time' => now()]
		);
		if ($rst !== false) {
			$this->clearStaticCache();
			$this->success('发布成功');
		}
		$this->error('发布失败');
	}

	private function clearStaticCache()
	{
		$tplRoot = ROOT_PATH . 'template';
		if (!is_dir($tplRoot)) {
			return;
		}
		foreach (scandir($tplRoot) ?: [] as $tplDir) {
			if ($tplDir === '.' || $tplDir === '..') continue;
			foreach (['computer', 'mobile'] as $device) {
				$staticDir = $tplRoot . '/' . $tplDir . '/' . $device . '/static';
				if (is_dir($staticDir)) {
					foreach (scandir($staticDir) ?: [] as $f) {
						if ($f === '.' || $f === '..') continue;
						$full = $staticDir . '/' . $f;
						if (is_file($full)) {
							@unlink($full);
						}
					}
				}
			}
		}
	}
}