<?php

namespace frame;

class Error
{
	public function register()
	{
		if (config('env', 'APP_DEBUG')) {
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
		} else {
			error_reporting(0);
		}
		set_error_handler([$this, 'errorDebug']);
		set_exception_handler([$this, 'exceptionDebug']);
		register_shutdown_function([$this, 'shutdownDebug']);
	}

	public function errorDebug($errno, $errStr, $errfile='', $errline='')
	{
		$this->errorEcho($errfile, $errline, $errStr);
	}

	public function exceptionDebug($exception)
	{
		$this->errorEcho($exception->getFile(), $exception->getLine(), $exception->getMessage());
	}

	public function shutdownDebug()
	{
		$_error = error_get_last();
		if ($_error) {
			$this->errorEcho($_error['file'], $_error['line'], $_error['message']);
		}
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
		if (IS_CLI) {
			echo 'File: '.$file.PHP_EOL;
			echo 'Line: '.$line.PHP_EOL;
			echo 'Error Message: '.$message.PHP_EOL;
		} else {
			if (config('env', 'APP_DEBUG')) {
				echo 'File: '.$file.'<br />';
				echo 'Line: '.$line.'<br />';
				echo 'Error Message: '.$message.'<br />';
				$this->echoParmas();
			} else {
				\App::set('app_error', $message);
				if (\App::get('router', 'path') != 'PageNotFound') {
					// redirect(url('pageNotFound'));
				}
			}
		}
		exit();
	}
}