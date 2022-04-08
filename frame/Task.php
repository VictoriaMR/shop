<?php 

namespace frame;

class Task
{
	const TASKPREFIX ='frame-task:';
	private $locker;

	public function __construct()
	{
		$this->locker = make('frame/Locker');
	}
	
	public function start($taskClass, $lockTimeout=0, $cas='')
	{
		$taskClass = $this->getStandClassName($taskClass);
		$lockKey = $this->getKeyByClassName($taskClass);
		if ($lockTimeout < 1) {
			$lockTimeout = config('task', 'timeout');
		}

		if ($cas == '') {
			$cas = $this->locker->lock($lockKey, $lockTimeout);
			if (!$cas) {
				return false;
			}
		}
		$process = [
			'class' => $taskClass,
			'lock' => [$lockKey, $cas],
		];
		return $this->run($process);
	}

	public function run($process)
	{
		$param = [];
		$param[] = $process['class'];
		$param[] = 'start';
		$param[] = 'lock='.base64_encode(json_encode($process['lock'], JSON_UNESCAPED_UNICODE));
		return $this->localRunPhp(implode(' ', $param));
	}

	public function localRunPhp($param)
	{
		$phpBin = config('task', 'phpbin');
		$cmd = $phpBin.' -f '.ROOT_PATH.'command '.$param;
		if (isWin()) {
			pclose(popen('start /B '.$cmd.' 1>NUL 2>NUL', 'r'));
		} else {
			$out = [];
			$rstSign = '';
			exec($cmd.' > /dev/null 2>&1 &', $out, $rstSign);
		}
		config('env', 'APP_DEBUG') && make('frame/Debug')->runlog($cmd, 'task');
		return true;
	}

	protected function getStandClassName($classname)
	{
		return strtr($classname, '-', '/');
	}

	public function getKeyByClassName($classname)
	{
		return strtr($classname, ['\\'=>'-', DS=>'-']);
	}
}