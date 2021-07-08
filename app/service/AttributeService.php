<?php 

namespace app\service;

use app\service\Base as BaseService;

/**
 * 	属性类
 */
class AttributeService extends BaseService
{
	const CACHE_KEY = 'PRODUCT_ATTRIBUTE_CACHE';

	public function addNotExist($name)
	{
		if (empty($name)) {
			return false;
		}
        $name = trim($name);
        $info = $this->getInfoByName($name);
        if (!empty($info)) {
            return $info['attr_id'];
        }

        $translateService = make('app\service\TranslateService');
        $data = [
            'name' => $name,
            'sort' => 0,
        ];
        $attrId = make('App\Models\Attribute')->create($data);
        //不设置多语言
        $attrLanModel = make('App\Models\AttributeLanguage');
        $data = [
            'attr_id' => $attrId,
            'lan_id' => 1,
            'name' => $name,
        ];
        $attrLanModel->create($data);
        return $attrId;
	}

    public function getInfoByName($name)
    {
        return make('App\Models\Attribute')->getInfoByWhere(['name' => $name]);
    }

    public function getInfo($attrId=null, $lanId=null)
    {
        if (empty($attrId)) {
            return false;
        }
        if (!is_array($attrId)) {
            $attrId = [$attrId];
        }
        $info = make('App\Models\Attribute')->whereIn('attr_id', $attrId)->field('attr_id, name')->get();
        if ($lanId > 0 && $lanId != env('DEFAULT_LANGUAGE_ID')) {
            $tempData = make('App\Models\AttributeLanguage')->whereIn('attr_id', $attrId)->where('lan_id', $lanId)->field('attr_id, name')->get();
            $tempData = array_column($tempData, 'name', 'attr_id');
            foreach ($info as $key => $value) {
                $info[$key]['name'] = $tempData[$value['attr_id']];
            }
        }
        return $info;
    }
}