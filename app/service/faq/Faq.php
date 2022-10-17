<?php 

namespace app\service\faq;
use app\service\Base;

class Faq extends Base
{
    protected function getModel()
    {
        $this->baseModel = make('app/model/faq/Faq');
    }
}