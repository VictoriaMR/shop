<?php

namespace template\clothes\controller\mobile;
use app\controller\Base;

class Index extends Base
{
    public function index()
    {
        html()->addCss();
        html()->addCss('common/product_list');

        html()->addJs();
        html()->addJs('lazysizes', false);
    }
}