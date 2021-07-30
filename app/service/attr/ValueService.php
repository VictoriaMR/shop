<?php 

namespace app\service\attr;

use app\service\Base

/**
 * 	属性值类
 */
class ValueService extends Base
{
	const CACHE_KEY = 'PRODUCT_ATTRVALUE_CACHE';

	public function addNotExist($name)
    {
        if (empty($name)) {
            return false;
        }
        $name = trim($name);
        $info = $this->getInfoByName($name);
        if (!empty($info)) {
            return $info['attv_id'];
        }
        $data = [
            'name' => $name,
            'sort' => 0,
        ];
        $translateService = make('app\service\TranslateService');
        $attvId = make('App\Models\Attrvalue')->create($data);
        //设置多语言
        $attrLanModel = make('App\Models\AttrvalueLanguage');
        $data = [
            'attv_id' => $attvId,
            'lan_id' => 1,
            'name' => $name,
        ];
        $attrLanModel->create($data);
        return $attvId;
    }

    public function getInfoByName($name)
    {
        return make('App\Models\Attrvalue')->getInfoByWhere(['name' => $name]);
    }

    public function getInfo($attvId=null, $lanId=null)
    {
        if (empty($attvId)) {
            return false;
        }
        if (!is_array($attvId)) {
            $attvId = [$attvId];
        }
        $info = make('App\Models\Attrvalue')->whereIn('attv_id', $attvId)->field('attv_id, name')->get();
        if ($lanId > 0 && $lanId != env('DEFAULT_LANGUAGE_ID')) {
            $tempData = make('App\Models\AttrvalueLanguage')->whereIn('attv_id', $attvId)->where('lan_id', $lanId)->field('attv_id, name')->get();
            $tempData = array_column($tempData, 'name', 'attv_id');
            foreach ($info as $key => $value) {
                $info[$key]['name'] = $tempData[$value['attv_id']];
            }
        }
        return $info;
    }
}