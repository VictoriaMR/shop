<?php 

namespace app\service\faq;
use app\service\Base;

class Group extends Base
{
    protected function getModel()
    {
        $this->baseModel = make('app/model/faq/Group');
    }
}