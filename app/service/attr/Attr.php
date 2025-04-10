<?php 

namespace app\service\attr;

class Attr
{
    public function __call($func, $arg)
    {
        return service('attr/'.ucfirst($func));
    }
}