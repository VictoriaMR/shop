<?php 

namespace app\service;
use app\service\Base;

class ProductSpuDataService extends BaseService
{
	public function getModel()
    {
        $this->baseModel = make('app/model/ProductSpuData');
    }
}