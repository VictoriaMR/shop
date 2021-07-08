<?php 

namespace app\service;

use app\service\Base as BaseService;
use App\Models\Category;

/**
 * 	分类类
 */
class CategoryService extends BaseService
{
    protected static $constantMap = [
        'base' => Category::class,
    ];

    public function __construct(Category $model)
    {
        $this->baseModel = $model;
    }

	public function create(array $data)
	{
		if (empty($data['name'])) {
			return false;
		}
        $name = trim($data['name']);
        $insert = [
            'parent_id' => $data['parent_id'] ?? 0,
            'name' => $name,
            'avatar' => $data['avatar'] ?? '',
            'sort' => $data['sort'] ?? 0,
        ];
        $cateId = $this->baseModel->create($insert);
        //设置多语言
        $cateLanModel = make('App\Models\CategoryLanguage');
        $translateService = make('app\service\TranslateService');
        $lanList = make('app\service\LanguageService')->getInfo();
        foreach ($lanList as $key => $value) {
            if ($value['code'] == 'zh') {
                $tempName = $name;
            } else {
                $tempName = $translateService->getTranslate($name, $value['tr_code']);
            }
            if (empty($tempName)) continue;
            $data = [
            	'cate_id' => $cateId,
            	'lan_id' => $value['lan_id'],
            	'name' => $tempName,
            ];
            $cateLanModel->create($data);
        }
        return $cateId;
	}

    public function getInfo($cateId)
    {
        $cateId = (int) $cateId;
        if (empty($cateId)) {
            return [];
        }
        $info = $this->baseModel->loadData($cateId);
        if (empty($info)) {
            return [];
        }
        $info['avatar_format'] = empty($info['avatar']) ? siteUrl('image/common/noimg.png') : mediaUrl($info['avatar'], 200);
        return $info;
    }

    public function getLanguage($cateId)
    {
        $cateId = (int) $cateId;
        if (empty($cateId)) {
            return [];
        }
        return make('App\Models\CategoryLanguage')->getListByWhere(['cate_id' => $cateId]);
    }

    public function getList(array $where=[])
    {
        $list = $this->baseModel->where($where)->orderBy('sort', 'asc')->get();
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                if (empty($value['avatar'])) {
                    $value['avatar'] = siteUrl('image/common/noimg.png');
                } else {
                    $value['avatar'] = mediaUrl($value['avatar'], 200);
                }
                $list[$key] = $value;
            }
        }
        return $list;
    }

    public function getListFormat()
    {
        $list = $this->getList();
        if (empty($list)) return [];
        $list = $this->listFormat($list, 0, 0);
        $returnData = [];
        $this->arrayFormat($list, $returnData);
        return $returnData;
    }

    protected function listFormat($list, $parentId=0, $lev=0) 
    {
        $returnData = [];
        foreach ($list as $value) {
            $value['level'] = $lev;
            if ($value['parent_id'] == $parentId) {
                $temp = $this->listFormat($list, $value['cate_id'], $value['level'] + 1);
                if (!empty($temp)) {
                    $value['son'] = $temp;
                }
                $returnData[] = $value;
            }
        }
        return $returnData;
    }

    protected function arrayFormat($list, &$returnData)
    {
        foreach ($list as $value) {
            $temp = $value;
            unset($temp['son']);
            $returnData[] = $temp;
            if (!empty($value['son'])) {
                $this->arrayFormat($value['son'], $returnData);
            }
        }
        return true;
    }

    public function setNxLanguage($cateId, $lanId, $name)
    {
        if (empty($cateId) || empty($lanId) || empty($name)) {
            return false;
        }
        $model = make('App\Models\CategoryLanguage');
        $where = ['cate_id'=>$cateId, 'lan_id'=>$lanId];
        if ($model->getCount($where)) {
            return $model->where($where)->update(['name' => $name]);
        } else {
            $where['name'] = $name;
            return $model->insert($where);
        }
    }

    public function hasChildren($id)
    {
        return $this->baseModel->where('parent_id', $id)->count() > 0;
    }

    public function hasProduct($id)
    {
        return make('App\Models\ProductCategoryRelation')->where('cate_id', $id)->count() > 0;
    }

    protected function deleteDataById($cateId)
    {
        $result = $this->baseModel->deleteById($cateId);
        if ($result) {
            $result = make('App\Models\CategoryLanguage')->where('cate_id', $cateId)->delete();
        }
    }

    public function addCateProRelation($spuId, array $cateIds)
    {
        if (empty($spuId) || empty($cateIds)) return false;
        $insert = [];
        $model = make('App\Models\ProductCategoryRelation');
        foreach ($cateIds as $key => $value) {
            $where = [
                'cate_id' => $value,
                'spu_id' => $spuId,
            ];
            if (!$model->getCount($where)) {
                $insert[] = $where;
            }
        }
        return $model->insert($insert);
    }

    public function getSpuIdByCateId($cateId)
    {
        $result = make('App\Models\ProductCategoryRelation')->where('cate_id', $cateId)->field('spu_id')->get();
        return array_column($result, 'spu_id');
    }

    public function getRelationList(array $where=[])
    {
        return make('App\Models\ProductCategoryRelation')->where($where)->field('cate_id,spu_id')->get();
    }

    public function updateStat()
    {
        $result = $this->baseModel->table('product_category_relation a')->leftJoin('product_spu b', 'a.spu_id', 'b.spu_id')->field('a.cate_id, SUM(b.sale_total) AS sale_total, SUM(b.visit_total) AS visit_total')->where('b.status', 1)->groupBy('a.cate_id')->get();
        if (!empty($result)) {
            $result = array_column($result, null, 'cate_id');
            foreach ($result as $key => $value) {
                $data = [];
                if (isset($value['sale_total'])) {
                    $data['sale_total'] = (int) $value['sale_total'];
                }
                if (isset($value['visit_total'])) {
                    $data['visit_total'] = (int) $value['visit_total'];
                }
                if (empty($data)) continue;
                $this->baseModel->updateDataById($key, $data);
            }
            //更新父类数据
            $result = $this->baseModel->where('parent_id', '>', 0)->field('parent_id,SUM(sale_total) AS sale_total, SUM(visit_total) AS visit_total')->groupBy('parent_id')->get();
            if (!empty($result)) {
                $result = array_column($result, null, 'parent_id');
                foreach ($result as $key => $value) {
                    $data = [];
                    if (isset($value['sale_total'])) {
                        $data['sale_total'] = (int) $value['sale_total'];
                    }
                    if (isset($value['visit_total'])) {
                        $data['visit_total'] = (int) $value['visit_total'];
                    }
                    if (empty($data)) continue;
                    $this->baseModel->updateDataById($key, $data);
                }
            }
        }
        return true;
    }

    public function getHotCategory($size=8)
    {
        $list = $this->baseModel->field('(sale_total+visit_total) AS sort_number, cate_id, name, avatar')->where('parent_id', '>', 0)->orderBy(['sort_number'=>'desc', 'sort' => 'asc'])->page(1, $size)->get();
        foreach ($list as $key => $value) {
            if (empty($value['avatar'])) {
                $value['avatar'] = siteUrl('image/common/noimg.png');
            } else {
                $value['avatar'] = mediaUrl($value['avatar'], 200);
            }
            $list[$key] = $value;
        }
        return $list;
    }
}