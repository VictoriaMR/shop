<?php 

namespace app\service\purchase;

class Purchase
{
    public function channel()
    {
        return service('purchase/Channel');
    }

    public function product()
    {
        return service('purchase/Product');
    }

    public function item()
    {
        return service('purchase/ProductItem');
    }

    public function shop()
    {
        return service('purchase/Shop');
    }
}