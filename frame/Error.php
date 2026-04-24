<?php

namespace frame;

class Error
{
	public function register()
	{
		error_reporting(E_ALL);
		set_error_handler([$this, 'handleError']);
		set_exception_handler([$this, 'handleException']);
		register_shutdown_function([$this, 'handleShutdown']);
	}

	public function handleError($code, $message, $file, $line)
	{
		$this->errorEcho([
			'code' => $code,
			'file' => $file,
			'line' => $line,
			'message' => $message,
		]);
	}

	public function handleException(\Throwable $e)
	{
		$this->errorEcho([
			'code' => $e->getCode(),
			'file' => $e->getFile(),
			'line' => $e->getLine(),
			'message' => $e->getMessage(),
			'trace' => $e->getTrace(),
		]);
	}

	public function handleShutdown()
	{
		$data = error_get_last();
		if ($data) {
			$this->errorEcho([
				'code' => $data['type'],
				'file' => $data['file'],
				'line' => $data['line'],
				'message' => $data['message'],
			]);
		}
	}

	protected function errorEcho($data)
	{
		$log = "[{$data['code']} - ".$this->errorType($data['code'])."] {$data['message']} [{$data['file']}:{$data['line']}]";
		echo isDebug() ? $log : '500 Internal Server Error';
		echo PHP_EOL;
		frame('Debug')->runlog($log, 'error');
	}

	private static $errorTypes = [
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
		E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
		E_DEPRECATED => 'E_DEPRECATED',
		E_USER_DEPRECATED => 'E_USER_DEPRECATED',
	];

	private function errorType($code)
	{
		return self::$errorTypes[$code] ?? $code;
	}
}