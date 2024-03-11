<?php

namespace app\service;

abstract class Base
{
    protected $baseModel;
    protected $_error = [];
    protected $_model;

    private function __clone() {}
    
    protected function getModel()
    {
        if (!$this->_model) {
            $this->_model = str_replace('\\service\\', '\\model\\', get_called_class());
        }
        $this->baseModel = make($this->_model);
    }

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