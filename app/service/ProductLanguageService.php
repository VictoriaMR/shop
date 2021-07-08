<?php 

namespace app\service;

use app\service\Base as BaseService;
use App\Models\ProductLanguage;

/**
 * 	产品语言类
 */
class ProductLanguageService extends BaseService
{
	protected static $constantMap = [
        'base' => ProductLanguage::class,
    ];

	public function __construct(ProductLanguage $model)
    {
        $this->baseModel = $model;
    }

	public function create(array $data)
	{
		if ($this->baseModel->isExist($data['spu_id'], $data['lan_id'])) {
			return false;
		} else {
			return $this->baseModel->create($data);
		}
	}

	public function getText($spuId, $lanId)
	{
		return $this->baseModel->getInfoByWhere(['spu_id'=>(int)$spuId, 'lan_id'=>(int)$lanId], 'name')['name'] ?? '';
	}
}