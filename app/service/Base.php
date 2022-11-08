<?php

namespace app\service;

abstract class Base
{
    protected $baseModel;
    protected $_error = [];
    protected $_model = [];

    private function __clone() {}
    
    protected function getModel()
    {
        if (!empty($this->_model)) {
            if (is_array($this->_model)) {
                if (count($this->_model) == 1) {
                    $this->baseModel = make(current($this->_model));
                } else {
                    foreach ($this->_model as $key=>$value) {
                        $this->$$key = make($value);
                    }
                }
            } else {
                $this->baseModel = make($this->_model);
            }
        }
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