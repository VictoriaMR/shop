<?php 

namespace app\service;

use app\service\Base as BaseService;
use App\Models\ProductSpuData;

/**
 * 	产品Spu数据类
 */
class ProductSpuDataService extends BaseService
{
	protected static $constantMap = [
        'base' => ProductSpuData::class,
    ];

	public function __construct(ProductSpuData $model)
    {
        $this->baseModel = $model;
    }

	public function create(array $data)
	{
		return $this->baseModel->insertGetId($data);
	}

	public function isExist(array $where)
	{
		return $this->baseModel->getCount($where) > 0;
	}
}