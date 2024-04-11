<?php 

namespace app\service\login;
use app\service\Logger as Base;

class Logger extends Base
{
	public function getTypeList()
    {
        return [
            $this->getConst('TYPE_LOGIN') => '登入',
            $this->getConst('TYPE_LOGOUT') => '登出',
            $this->getConst('TYPE_LOGIN_FAIL') => '登入失败',
            $this->getConst('TYPE_BEHAVIOR') => '操作',
        ];
    }
}