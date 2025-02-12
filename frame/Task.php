<?php 

namespace frame;

class Task
{
	const TASKPREFIX ='frame:task:';
	
	public function start($taskClass)
	{
		$param = [];
		$param[] = str_replace('-', '/', $taskClass);
		$param[] = 'start';
		return $this->localRunPhp(implode(' ', $param));
	}

	public function localRunPhp($param)
	{
		$phpBin = config('task', 'phpbin');
		$cmd = $phpBin.' -f '.ROOT_PATH.'command '.$param;
		if (isWin()) {
			pclose(popen('start /B '.$cmd.' 1>NUL 2>NUL', 'r'));
		} else {
			exec($cmd.' > /dev/null 2>&1 &', [], '');
		}
		frame('Debug')->runlog($cmd, 'task');
		return true;
	}
	
	public function getClassKey($classname)
	{
		return str_replace(['\\', '/'], '-', $classname);
	}

	public function getTaskList($main = false)
	{
		$files = scandir(ROOT_PATH.'app/task/main');
		$list = [];
		if ($main) {
			$list[] = 'app/task/MainTask';
		}
		foreach ($files as $value) {
			if ($value == '.' || $value == '..') continue;
			$list[] = 'app/task/main/'.str_replace('.php', '', $value);
		}
		$listInfo = $this->getInfo();
		$list = array_flip($list);
		foreach ($list as $key=>$value) {
			$classKey = $this->getClassKey($key);
			if (empty($listInfo[$classKey])) {
				$class = \App::make($key);
				$listInfo[$classKey] = $class->config;
			}
			$list[$key] = $listInfo[$classKey];
		}
		return $list;
	}

	protected function getKey($key)
	{
		return self::TASKPREFIX.$this->getClassKey($key);
	}

	public function boot($key, $value)
	{
		$classKey = $this->getClassKey($key);
		$info = $this->getInfo($classKey);
		if (empty($info)) {
			$class = \App::make($key);
			$info = $class->config;
			$info['next_run'] = $class->getNextTime($info['cron']);
		}
		$info['boot'] = $value.'ing';
		$info['status'] = 'stop';
		$this->setInfo($classKey, $info);
		if ($classKey == 'app-task-MainTask' && $value == 'on' && ($info['status'] ?? '') != 'running') {
			$this->start($key);
		}
		return true;
	}

	public function setInfo($field, array $data)
	{
		return redis(2)->hSet($this->getKey('list'), $field, $data);
	}

	public function getInfo($field='')
	{
		if ($field) {
			return redis(2)->hGet($this->getKey('list'), $field);
		} else {
			return redis(2)->hGetAll($this->getKey('list'));
		}
	}

	protected function noticeTask($key)
	{
		$key = $this->getClassKey($key);
		$info = $this->getInfo($key);
		if (empty($info) || $info['boot'] != 'on') {
			return false;
		}
		$info['next_run'] = time();
		$this->setInfo($key, $info);
		return true;
	}

	public function taskStart($name)
	{
		return $this->noticeTask('app/task/main/'.$name);
	}
}