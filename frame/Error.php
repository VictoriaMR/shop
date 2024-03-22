<?php

namespace frame;

class Error
{
    public function register()
    {
        error_reporting(E_ALL);
        set_error_handler(array($this, 'appError'));
        set_exception_handler(array($this, 'appException'));
        // register_shutdown_function(array($this, 'appShutdown'));
    }

    public function appError($errno, $errStr, $errfile='', $errline='')
    {
        $this->errorEcho(['code'=>$errno, 'file'=>$errfile, 'line'=>$errline, 'message'=>$errStr]);
    }

    public function appException($exception)
    {
        $this->errorEcho(['code'=>$exception->getCode(), 'file'=>$exception->getFile(), 'line'=>$exception->getLine(), 'message'=>$exception->getMessage(), 'trace'=>$exception->getTrace()]);
    }

    public function appShutdown()
    {
        if (error_get_last()) {
            $_error = error_get_last();
            $this->errorEcho(['code'=>$_error['code'], 'file'=>$_error['file'], 'line'=>$_error['line'], 'message'=>$_error['message']]);
        }
        //关闭session
        session()->close();
    }

    protected function errorEcho($data)
    {
        $log = "[{$data['code']} - ".$this->errorType($data['code'])."] {$data['message']} [{$data['file']}:{$data['line']}]";
        make('frame/Debug')->runlog($log);
        $str = 'Error:'.$data['code'].' - '.$this->errorType($data['code']);
        if (isCli() || isDebug()) {
            $br = isAjax() ? "\n" : (isCli() ? PHP_EOL : '<br />');
            $str .= $br;
            $str .= 'File: '.$data['file'].$br;
            $str .= 'Line: '.$data['line'].$br;
            $str .= 'Error Message: '.$data['message'].$br;
            if (isset($data['trace'])) {
                $str .= 'Stack trace:'.$br;
                foreach ($data['trace'] as $key=>$value) {
                    $str .= sprintf('%s# %s(%s): %s %s %s()', $key, $value['file'], $value['line'], $value['class']??'', $value['type']??'', $value['function']??'').$br;
                }
            }
        } else {

        }
        http_response_code(500);
        if (isAjax()) {
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode(['code'=>500, 'msg'=>$str], JSON_UNESCAPED_UNICODE);
        } else {
            echo $str;
        }
        exit();
    }

    private function errorType($code)
    {
        switch($code) {
            case 0:
                return 'Fatal error';
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }
        return $code;
    }
}