<?php 

namespace app\service\faq;
use app\service\Base;

class FaqLanguage extends Base
{
    protected function getModel()
    {
        $this->baseModel = make('app/model/faq/FaqLanguage');
    }

    public function setNxLanguage($id, $lanId, $title, $content)
    {
        if (empty($id) || empty($name) || empty($content)) {
            return false;
        }
        $where = ['faq'=>$id, 'lan_id'=>$lanId];
        if ($this->getCountData($where)) {
            return $this->updateData($where, ['name'=>$name, 'content'=>$content]);
        } else {
            $where['name'] = $name;
            $where['content'] = $content;
            return $this->insert($where);
        }
    }
}