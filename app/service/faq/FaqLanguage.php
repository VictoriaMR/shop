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
        if (empty($id) || empty($title) || empty($content)) {
            return false;
        }
        $where = ['faq_id'=>$id, 'lan_id'=>$lanId];
        if ($this->getCountData($where)) {
            return $this->updateData($where, ['title'=>$title, 'content'=>$content]);
        } else {
            $where['title'] = $title;
            $where['content'] = $content;
            return $this->insert($where);
        }
    }
}