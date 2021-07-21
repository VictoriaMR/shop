<?php 

namespace app\service;
use app\service\Base;

class AttachmentService extends Base
{	

	protected function getModel()
    {
        $this->baseModel = make('app/model/Attachment');
    }

     public function addIfNot(array $data)
    {
        if (empty($data['name'])) {
            return false;
        }
        $info = $this->getAttachmentByName($data['name']);
        if (!empty($info)) {
            return $info['attach_id'];
        }
        return $this->create($data);
    }

    public function create(array $data)
    {
        if (empty($data['name'])) {
            return false;
        }
        return $this->baseModel->create($data);
    }

    public function isExist($name)
    {
        if (empty($data['name'])) {
            return false;
        }
    	return $this->baseModel->isExist($name);
    }

    public function getAttachmentByName($name, $width='')
    {
        if (empty($name)) return [];
    	$info = $this->baseModel->getAttachmentByName($name);
        return $this->urlInfo($info, $width);;
    }

    public function getAttachmentById($attachId)
    {
        $attachId = (int) $attachId;
        if (empty($attachId)) return [];
        $info = $this->baseModel->loadData($attachId);
        return $this->urlInfo($info);
    }

    public function getAttachmentListById($attachId)
    {
        if (empty($attachId)) return [];
        $list = $this->baseModel->getListById($attachId);
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                $list[$key] = $this->urlInfo($value);
            }
        }
        return $list;
    }

    public function urlInfo($info, $width='')
    {
        if (!empty($info)) {
            $info['url'] = env('FILE_CENTER_DOMAIN').$info['cate'].DS.$info['name'].(empty($width) ? '' : DS.$width).'.'.$info['type'];
        }
        return $info;
    }
}