<?php

namespace frame;

class Error
{
	public function register()
	{
		if (config('env', 'APP_DEBUG')) {
			error_reporting(E_ALL);
		} else {
			error_reporting(0);
		}
		set_exception_handler(array($this, 'exceptionDebug'));
		set_error_handler(array($this, 'errorDebug'));
		register_shutdown_function(array($this, 'shutdownDebug'));
	}

	public function errorDebug($errno, $errStr, $errfile='', $errline='')
	{
		$this->errorEcho($errfile, $errline, $errStr);
	}

	public function exceptionDebug($exception)
	{
		$this->errorEcho($exception->getFile(), $exception->getLine(), $exception->getMessage());
		foreach ($exception->getTrace() as $value) {
			$this->errorEcho($value['file'], $value['line'], $value['class'].$value['type'].$value['function']);
		}
	}

	public function shutdownDebug()
	{
		$_error = error_get_last();
		if ($_error) {
			$this->errorEcho($_error['file'], $_error['line'], $_error['message']);
		}
		$this->echoParmas();
		exit();
	}

	protected function echoParmas()
	{
		$param = request()->input();
		if (!empty($param)) {
			$count = 0;
			foreach ($param as $key => $value) {
				if ($count == 0) {
					echo 'Parmas: '.$key.' => '.$value.'<br />';
				} else {
					echo '---------'.$key.' => '.$value.'<br />';
				}
				$count ++;
			}
		}
	}

	protected function errorEcho($file, $line, $message)
	{
		make('frame/Debug')->runlog($file.PHP_EOL.$line.PHP_EOL.$message);
		if (isCli()) {
			echo 'File: '.$file.PHP_EOL;
			echo 'Line: '.$line.PHP_EOL;
			echo 'Error Message: '.$message.PHP_EOL;
		} else {
			if (config('env', 'APP_DEBUG')) {
				echo 'File: '.$file.'<br />';
				echo 'Line: '.$line.'<br />';
				echo 'Error Message: '.$message.'<br />';
			} else {
				\App::set('app_error', $message);
			}
		}
	}
}