<?php

namespace app\service;

class Base
{
    protected $baseModel;

    public function __call($func, $arg)
    {
        return $this->baseModel->$func(...$arg);
    }
}