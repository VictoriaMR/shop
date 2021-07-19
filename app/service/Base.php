<?php

namespace app\service;

abstract class Base
{
    protected $baseModel;
    private function __clone() {}
    
    abstract protected function getModel();

    public function __call($func, $arg)
    {
        if (is_null($this->baseModel)) {
            $this->getModel();
        }
        return $this->baseModel->$func(...$arg);
    }
}