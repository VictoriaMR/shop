<?php

namespace app\controller\admin;
use app\controller\AdminBase;
use app\task\TaskDriver;

class Task extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '定时任务管理',
		];
		$this->_default = '任务管理';
		$this->_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['modifyTask'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();
		$list = frame('Task')->getTaskList(true);
		$this->view([
			'taskList' => $list,
		]);
	}

	protected function modifyTask()
	{
		$type = ipost('type');
		$key = trim(ipost('key', ''));
		$tasker = frame('Task');
		switch ($type) {
			case 'start':
			case 'stop':
				if (empty($key)) {
					$this->error('未指定任务');
				}
				$tasker->boot($key, $type === 'start' ? 'on' : 'off');
				break;
			case 'run':
				if (empty($key) || $key === TaskDriver::MAIN_TASK) {
					$this->error('请指定要执行的子任务');
				}
				$tasker->noticeTask($key, true);
				break;
			case 'start-all':
			case 'stop-all':
				$list = $tasker->getTaskList(true);
				$targetBoot = ($type === 'start-all') ? 'on' : 'off';
				foreach ($list as $taskKey => $value) {
					$tasker->boot($taskKey, $targetBoot);
				}
				break;
			case 'reset':
				if ($key !== TaskDriver::MAIN_TASK) {
					$this->error('非主进程任务不能重置');
				}
				$info = $tasker->getInfo(TaskDriver::MAIN_TASK);
				if (is_array($info)) {
					if (($info['status'] ?? '') === 'running') {
						$info['boot'] = 'off';
						$info['status'] = 'stop';
						$info['process_pid'] = 0;
						$tasker->setInfo(TaskDriver::MAIN_TASK, $info);
						$tasker->signal();
						usleep(100000);
					}
					$info['status'] = 'stop';
					$info['boot'] = 'on';
					$info['process_pid'] = 0;
					$tasker->setInfo(TaskDriver::MAIN_TASK, $info);
				}
				$tasker->start(TaskDriver::MAIN_TASK);
				break;
			default:
				$this->error('未知操作类型');
		}
		$this->success('操作成功');
	}
}