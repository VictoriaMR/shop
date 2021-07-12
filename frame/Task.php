<?php 

namespace frame;

class Task
{
	public function start($taskClass='', $lockTimeout=0, $cas='')
	{
		if (empty($taskClass)) {
			$taskClass = 'app\task\MainTask';
		} else {
			$taskClass = $this->getStandClassName($taskClass);
		}
		if ($lockTimeout < 1) {
			$lockTimeout = config('task.timeout');
		}
		$lockKey = $this->getKeyByClassName($taskClass);
		$locker = make('frame/Locker');
		if (empty($cas)) {
			if ($locker->lock($lockKey, $lockTimeout)) {
				$cas = $locker->holdLock($lockKey);
				$process = [
					'class' => $taskClass,
					'lock' => [$lockKey, $cas],
				];
				return $this->run($process);
			}
		} else {
			$process = [
				'class' => $taskClass,
				'lock' => [$lockKey, $cas],
			];
			return $this->run($process);
		}
		return true;
	}

	protected function run($process)
	{
		list($lock, $cas) = $process['lock'];
		$locker = make('frame/Locker');
		if ($locker->getLock($lock, $cas)) {
			$locker->holdLock($lock);
			return $this->localRun($process);
		} else {
			make('frame/Debug')->runlog(explode(', ', $process), 'task-error');
		}
		return false;
	}

	public function localRun($process)
	{
		$param = [];
		$param[] = $process['class'];
		$param[] = 'start';
		$param[] = 'lock='.base64_encode(json_encode($process['lock'], JSON_UNESCAPED_UNICODE));
		return $this->localRunPhp(implode(' ', $param));
	}

	public function localRunPhp($param)
	{
		$phpBin = config('task.phpbin');
		$cmd = $phpBin.' -f '.ROOT_PATH.'command '.$param;
		if (request()->isWin()) {
			pclose(popen('start /B '.$cmd.' 1>NUL 2>NUL', 'r'));
		} else {
			$out = [];
			$rstSign = '';
			exec($cmd.' > /dev/null 2>&1 &', $out, $rstSign);
		}
		env('APP_DEBUG') && make('frame/Debug')->runlog($cmd, 'task');
		return true;
	}

	protected function getStandClassName($classname)
	{
		return trim($classname, ' \t\n\r\0\x0B\\');
	}

	public function getKeyByClassName($classname)
	{
		return str_replace('\\', '-', $classname);
	}
}