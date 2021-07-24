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
		$info = $this->loadData(['name'=>$data['name']], 'attach_id');
		if (empty($info)) {
			$data['add_time'] = now();
			return $this->insertGetId($data);
		}
		return $info['attach_id'];
	}

	public function getAttachmentByName($name, $width='')
	{
		$info = $this->loadData(['name'=>$name]);
		if (empty($info)) {
			return false;
		}
		return $this->urlInfo($info, $width);;
	}

	public function getAttachmentById($attachId)
	{
		$info = $this->loadData($attachId);
		if (empty($info)) {
			return false;
		}
		return $this->urlInfo($info);
	}

	public function urlInfo($info, $width='')
	{
		$info['url'] = env('FILE_CENTER_DOMAIN').$info['cate'].DS.$info['name'].($width == '' ? '' : DS.$width).'.'.$info['type'];
		return $info;
	}

	public function getList($where)
	{
		$list = $this->getListData($where);
		foreach ($list as $key => $value) {
			$list[$key] = $this->urlInfo($value, '400');
		}
		return $list;
	}
}