<?php 

namespace app\service\purchase;

class Purchase
{
    public function channel()
    {
        return make('app/service/purchase/Channel');
    }

    public function product()
    {
        return make('app/service/purchase/Product');
    }
}