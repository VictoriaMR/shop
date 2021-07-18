<?php 

namespace app\service;
use app\service\Base;

class SiteService extends Base
{
    const CACHE_KEY = 'SITE_LIST_CACHE';

	public function getLanguage(array $where)
    {
        return make('app/model/SiteLanguage')->getListData($where);
    }

    public function setNxLanguage($siteId, $name, $lanId, $value)
    {
        if (empty($siteId) || empty($name) || empty($lanId) || empty($value)) {
            return false;
        }
        $model = make('app/model/SiteLanguage');
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
        return make('app/model/Site')->getListData($where);
    }

    public function getListCache()
    {
        $list = redis()->get(self::CACHE_KEY);
        if ($list === false) {
            $list = $this->getList();
            dd($list);
            redis()->set(self::CACHE_KEY, $list);
        }
        return $list;
    }
}