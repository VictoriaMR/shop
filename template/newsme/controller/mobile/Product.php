<?php

namespace template\newsme\controller\mobile;
use app\controller\Base;

class Product extends Base
{
    public function index()
    {
        html()->addCss('common/productList');
        html()->addJs('slider');
    }
}