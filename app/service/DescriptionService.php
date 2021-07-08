<?php 

namespace app\service;

use app\service\Base as BaseService;

/**
 * 	产品Spu数据类
 */
class DescriptionService extends BaseService
{
    public function setNotExit($name)
    {
    	if (empty($name)) {
    		return 0;
    	}
    	$model = make('App\Models\Description');
        $name = mb_substr($name, 0, 120, 'UTF-8');
    	$info = $model->getInfoByWhere(['name' => $name]);
    	if (empty($info)) {
    		$id = $model->insertGetId(['name' => $name]);
            //翻译
            $model = make('App\Models\DescriptionLanguage');
            $insert = [
                'desc_id' => $id,
                'lan_id' => 1,
                'name' => $name,
            ];
            make('App\Models\DescriptionLanguage')->insert($insert);
    	} else {
            $id = $info['desc_id'];
        }
    	return $id;
    }

    public function addDescRelation(array $insert)
    {
    	if (empty($insert)) {
    		return false;
    	}
    	$model = make('App\Models\ProductDescRelation');
    	if (!empty($insert[0]) && is_array($insert[0])) {
            foreach ($insert as $key => $value) {
                if ($model->getCount($value)) {
                    unset($insert[$key]);
                }
            }
        }
        return $model->insert($insert);
    }
}