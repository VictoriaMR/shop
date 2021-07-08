<?php 

namespace app\service;

use app\service\Base as BaseService;
use App\Models\Site;

class SiteService extends BaseService
{
    const CACHE_KEY = 'SITE_LIST_CACHE';
    public function __construct(Site $model)
    {
        $this->baseModel = $model;
    }

	public function getLanguage(array $where)
    {
        return make('App\Models\SiteLanguage')->getListByWhere($where);
    }

    public function setNxLanguage($siteId, $name, $lanId, $value)
    {
        if (empty($siteId) || empty($name) || empty($lanId) || empty($value)) {
            return false;
        }
        $model = make('App\Models\SiteLanguage');
        $where = ['site_id'=>$siteId, 'name'=>$name, 'lan_id'=>$lanId];
        if ($model->getCount($where)) {
            return $model->where($where)->update(['value' => $value]);
        } else {
            $where['value'] = $value;
            return $model->insert($where);
        }
    }

    public function getList(array $where=[])
    {
        return $this->baseModel->getListByWhere($where);
    }

    public function getListCache()
    {
        $list = redis()->get(self::CACHE_KEY);
        if ($list === false) {
            $list = $this->getList();
            redis()->set(self::CACHE_KEY, $list, -1);
        }
        return $list;
    }
}