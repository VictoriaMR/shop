<?php

namespace frame;

class Error
{
	private static $fatalTypes = [
		E_ERROR => true,
		E_PARSE => true,
		E_CORE_ERROR => true,
		E_COMPILE_ERROR => true,
		E_USER_ERROR => true,
		E_RECOVERABLE_ERROR => true,
	];

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
		return true;
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
		if ($data && isset(self::$fatalTypes[$data['type']])) {
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
		$log = "[{$data['code']} - " . $this->errorType($data['code']) . "] {$data['message']} [{$data['file']}:{$data['line']}]";
		$isFatal = isset(self::$fatalTypes[$data['code']]) || isset($data['trace']);

		frame('Debug')->runlog($log, 'error');

		if (isAjax()) {
			if ($isFatal || isDebug()) {
				if (!headers_sent()) {
					header('Content-Type: application/json; charset=utf-8');
				}
				echo json_encode([
					'code' => 0,
					'msg'  => isDebug() ? $log : 'Internal Server Error',
					'data' => [],
				], JSON_UNESCAPED_UNICODE);
				exit();
			}
			return;
		}

		// 致命错误/异常: 始终输出; 非致命错误: 仅 debug 输出
		if (isDebug()) {
			if (isCli()) {
				echo $log . PHP_EOL;
			} else {
				echo '<pre style="background:#424651;color:#fff;padding:16px 20px;border-radius:6px;font-family:Consolas,monospace;font-size:14px;line-height:1.6;margin:20px;word-break:break-all;white-space:pre-wrap;">' . htmlspecialchars($log);
				if (!empty($data['trace'])) {
					$traceStr = is_string($data['trace']) ? $data['trace'] : json_encode($data['trace'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
					echo "\n\nStack Trace:\n" . htmlspecialchars($traceStr);
				}
				echo '</pre>';
			}
		} elseif ($isFatal) {
			if (!headers_sent()) {
				http_response_code(500);
			}
			echo '500 Internal Server Error' . PHP_EOL;
		}
	}

	private function errorType($code)
	{
		return self::$errorTypes[$code] ?? $code;
	}
}