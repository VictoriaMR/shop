<?php

namespace template\newsme\controller\mobile;
use app\controller\Base;

class Cart extends Base
{
    public function index()
    {
        html()->addCss('common/productList');
    }
}