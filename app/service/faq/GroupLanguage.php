<?php 

namespace app\service\faq;
use app\service\Base;

class GroupLanguage extends Base
{
    protected function getModel()
    {
        $this->baseModel = make('app/model/faq/GroupLanguage');
    }

    public function setNxLanguage($id, $lanId, $name)
    {
        if (empty($id) || empty($name)) {
            return false;
        }
        $where = ['group_id'=>$id, 'lan_id'=>$lanId];
        if ($this->getCountData($where)) {
            return $this->updateData($where, ['name' => $name]);
        } else {
            $where['name'] = $name;
            return $this->insert($where);
        }
    }
}