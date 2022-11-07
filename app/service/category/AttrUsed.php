<?php 

namespace app\service\category;
use app\service\Base;

class AttrUsed extends Base
{
    protected function getModel()
    {
        $this->baseModel = make('app/model/category/AttrUsed');
    }
}