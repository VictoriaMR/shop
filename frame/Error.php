<?php

namespace frame;

class Error
{
	public function register()
	{
		error_reporting(E_ALL);
		set_error_handler(array($this, 'appError'));
		set_exception_handler(array($this, 'appError'));
		register_shutdown_function(array($this, 'appError'));
	}

	public function appError(...$arg)
	{
		if (empty($arg)) {
			if ($data = error_get_last()) {
				$data = ['code'=>$data['code'] ?? '', 'file'=>$data['file'], 'line'=>$data['line'], 'message'=>$data['message']];
			}
		} else {
			if (is_object($arg[0])) {
				$data = ['code'=>$arg[0]->getCode(), 'file'=>$arg[0]->getFile(), 'line'=>$arg[0]->getLine(), 'message'=>$arg[0]->getMessage(), 'trace'=>$arg[0]->getTrace()];
			} else {
				$data = ['code'=>$arg[0], 'file'=>$arg[2], 'line'=>$arg[3], 'message'=>$arg[1]];
			}
		}
		if ($data) $this->errorEcho($data);
	}

	protected function errorEcho($data)
	{
		$log = "[{$data['code']} - ".$this->errorType($data['code'])."] {$data['message']} [{$data['file']}:{$data['line']}]";
		if (isDebug()) {
			if (isAjax()) {
				\App::jsonRespone(0, $data, $log);
			} else {
				print_r($data);
				echo PHP_EOL.$log;
			}
		} else {
			if (isAjax()) {
				\App::jsonRespone(0, array(), '500 Internal Server Error');
			} else {
				redirect(url('pageNotFound'));
			}
		}
		frame('Debug')->runlog($log, 'error');
	}

	private function errorType($code)
	{
		$arr = [
			0 => 'Fatal',
			E_ERROR => 'E_ERROR',
			E_WARNING => 'E_WARNING',
			E_PARSE => 'E_PARSE',
			E_NOTICE => 'E_NOTICE',
			E_CORE_ERROR => 'E_CORE_ERROR',
			E_CORE_WARNING => 'E_CORE_WARNING',
			E_COMPILE_ERROR => 'E_COMPILE_ERROR',
			E_COMPILE_WARNING => 'E_COMPILE_WARNING',
			E_USER_ERROR => 'E_USER_ERROR',
			E_USER_WARNING => 'E_USER_WARNING',
			E_USER_NOTICE => 'E_USER_NOTICE',
			E_STRICT => 'E_STRICT',
			E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
			E_DEPRECATED => 'E_DEPRECATED',
			E_USER_DEPRECATED => 'E_USER_DEPRECATED',
		];
		return $arr[$code] ?? $code;
	}
}