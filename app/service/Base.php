<?php

namespace app\service;

abstract class Base
{
    protected $baseModel;
    protected $_error = [];

    private function __clone() {}
    
    abstract protected function getModel();

    public function __call($func, $arg)
    {
        if (is_null($this->baseModel)) {
            $this->getModel();
        }
        return $this->baseModel->$func(...$arg);
    }

    protected function setError($message)
    {
        $this->_error[] = $message;
    }

    public function getError()
    {
        return $this->_error;
    }
}