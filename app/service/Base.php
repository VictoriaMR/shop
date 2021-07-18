<?php

namespace app\service;

class Base
{
    protected $baseModel;

    public function __call($func, $arg)
    {
        if (is_null($this->baseModel)) {
            $this->getModel();
        }
        return $this->baseModel->$func(...$arg);
    }
}